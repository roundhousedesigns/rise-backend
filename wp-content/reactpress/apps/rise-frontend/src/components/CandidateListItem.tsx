import {
	Avatar,
	Card,
	Flex,
	Heading,
	LinkBox,
	LinkBoxProps,
	LinkOverlay,
	ListItem,
	Text,
} from '@chakra-ui/react';
import ColorCascadeBox from '@common/ColorCascadeBox';
import StarToggleIcon from '@common/StarToggleIcon';
import CandidateAvatarBadge from '@components/CandidateAvatarBadge';
import { SearchContext } from '@context/SearchContext';
import { useProfileUrl } from '@hooks/hooks';
import { Candidate } from '@lib/classes';
import useUserProfile from '@queries/useUserProfile';
import useViewer from '@queries/useViewer';
import { useContext } from 'react';
import { Link as RouterLink } from 'react-router-dom';

interface Props {
	candidate: Candidate;
	showToggle?: boolean;
	mini?: boolean;
}

const CandidateListItem = ({
	candidate,
	showToggle = true,
	mini = false,
	...props
}: Props & LinkBoxProps) => {
	const { id, image, slug, selfTitle } = candidate || {};

	const [profile] = useUserProfile(id ? id : 0);
	const profileUrl = useProfileUrl(slug);
	const { conflictRanges } = profile || {};
	const [{ loggedInId }] = useViewer();

	const {
		search: {
			filters: {
				filterSet: { jobDates },
			},
		},
	} = useContext(SearchContext);

	const hasDateConflict =
		conflictRanges && jobDates && jobDates.startDate ? jobDates.hasConflict(conflictRanges) : false;

	return id ? (
		<ListItem display='flex' alignItems='center'>
			<LinkBox aria-labelledby={`candidate-${id}`} flex={1} textDecoration='none' {...props}>
				<ColorCascadeBox spread={0.5}>
					<Card
						variant={mini ? 'listItemMini' : 'listItem'}
						w='full'
						py={2}
						transition='all 100ms ease'
						_light={{ bg: 'gray.100' }}
						_dark={{ bg: 'gray.700' }}
					>
						<Flex
							direction='row'
							justifyContent='flex-start'
							alignItems='center'
							flexWrap={{ base: 'wrap', md: 'nowrap' }}
							gap={{ base: 'initial', md: 0 }}
						>
							<Avatar
								size={mini ? 'sm' : 'md'}
								name={candidate.fullName()}
								flex='0 0 auto'
								mr={2}
								src={image}
								ignoreFallback={image ? true : false}
								aria-label={candidate.fullName() || 'Profile picture'}
							>
								<CandidateAvatarBadge reason={hasDateConflict ? 'dateConflict' : undefined} />
							</Avatar>
							<Flex flex='1' alignItems='center' flexWrap='wrap'>
								<Heading
									as='h3'
									id={`candidate-${id}`}
									variant='cardItemTitle'
									fontSize={mini ? 'md' : 'lg'}
									textAlign='left'
									flex={{ base: '0 0 100%', md: '1' }}
									mt={0}
									mb={{ base: '4px', md: 0 }}
								>
									<LinkOverlay
										as={RouterLink}
										to={`/${profileUrl}`}
										textDecoration='none'
										_light={{ color: 'text.dark' }}
										_dark={{ color: 'text.light' }}
									>
										{candidate.fullName() ? candidate.fullName() : 'No name'}
									</LinkOverlay>
								</Heading>
								{!mini && (
									<Text
										textAlign={{ base: 'left', md: 'right' }}
										ml={{ base: '0 !important', lg: 'initial' }}
										my={0}
										lineHeight={{ base: 'normal' }}
										fontSize='sm'
										noOfLines={2}
										flex={{ base: '0 0 100%', md: '1' }} // '1'}
										style={{ hyphens: 'auto' }}
										wordBreak='break-word'
									>
										{selfTitle}
									</Text>
								)}
							</Flex>
						</Flex>
					</Card>
				</ColorCascadeBox>
			</LinkBox>
			{showToggle && (
				<StarToggleIcon
					id={id}
					isDisabled={loggedInId === id}
					size={mini ? 'sm' : 'md'}
					mr={mini ? 0 : 2}
				/>
			)}
		</ListItem>
	) : (
		<></>
	);
};

export default CandidateListItem;

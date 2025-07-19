import {
	Badge,
	Box,
	BoxProps,
	Button,
	Card,
	Flex,
	FlexProps,
	Heading,
	LinkBox,
	LinkOverlay,
	Skeleton,
	Stack,
	Text,
	Wrap,
} from '@chakra-ui/react';
import RiseStar from '@common/icons/RiseStar';
import PositionsDisplay from '@common/PositionsDisplay';
import WrapWithIcon from '@common/WrapWithIcon';
import { Credit } from '@lib/classes';
import { decodeString, sortAndCompareArrays } from '@lib/utils';
import useLazyTaxonomyTerms from '@queries/useLazyTaxonomyTerms';
import { useEffect, useMemo, useState } from 'react';
import { FiBriefcase, FiMapPin, FiStar } from 'react-icons/fi';

interface Props {
	id?: string;
	credit: Credit;
	isEditable?: boolean;
	onClick?: () => void;
}
export default function CreditItem({
	credit,
	isEditable,
	onClick,
	...props
}: Props & BoxProps): JSX.Element {
	const {
		title,
		jobTitle,
		jobLocation,
		positions: { departments: departmentIds, jobs: jobIds } = { departments: [], jobs: [] },
		skills: skillIds,
		venue,
		workStart,
		workEnd,
		workCurrent,
		intern,
		fellow,
	} = credit || {};

	// Get jobs and skills terms from their IDs
	const [termList, setTermList] = useState<number[]>([]);
	const memoizedTermList = useMemo(() => termList, [termList]);

	const [getTerms, { loading: termsLoading }] = useLazyTaxonomyTerms();

	// Set the term ID list state
	useEffect(() => {
		if (!jobIds && !skillIds) return;

		const joinedTermList = jobIds.concat(skillIds);

		setTermList(joinedTermList);
	}, [jobIds, skillIds]);

	// Get jobs terms from their IDs
	useEffect(() => {
		if (!sortAndCompareArrays(termList, memoizedTermList) || termList.length === 0) return;

		getTerms({
			variables: {
				include: termList,
			},
		});
	}, [termList, memoizedTermList]);

	// const handleCreditKeyDown = (e: KeyboardEvent<HTMLAnchorElement>) => {
	// 	if (onClick === undefined) return;

	// 	if (e.key === 'Enter' || e.key === ' ') {
	// 		onClick();
	// 	}
	// };

	const yearString = () => {
		if (workStart && workEnd && workStart === workEnd) {
			return workStart;
		} else if (workStart && workEnd && !workCurrent) {
			return `${workStart} - ${workEnd}`;
		} else if (workStart && workCurrent) {
			return `${workStart} - Present`;
		} else if (workStart && !workEnd && !workCurrent) {
			return `${workStart}`;
		} else {
			return '';
		}
	};

	const InternFellowBadges = ({ ...props }: FlexProps): JSX.Element | null =>
		intern || fellow ? (
			<Flex color='brand.yellow' fontFamily='special' gap={1} fontSize='sm' {...props}>
				<RiseStar px={0} mx={0} fontSize='sm' />
				<Text textTransform='none' my={0}>
					{intern ? 'Internship' : ''}

					{intern && fellow ? <Text as='span'> + </Text> : ''}

					{fellow ? 'Fellowship' : ''}
				</Text>
			</Flex>
		) : null;

	return (
		<Box onClick={onClick} {...props}>
			<LinkBox aria-labelledby={`credit-${credit.id}`}>
				<Card
					cursor={isEditable ? 'pointer' : 'default'}
					tabIndex={0}
					// onKeyDown={handleCreditKeyDown}
					borderWidth={isEditable ? '2px' : '0'}
					borderStyle='dashed'
					borderColor='gray.500'
					transition='border-color .1s ease-out'
					_hover={isEditable ? { borderColor: 'gray.400' } : {}}
				>
					<Skeleton isLoaded={!termsLoading}>
						<Flex
							alignItems='flex-start'
							justifyContent='space-between'
							flexWrap={{ base: 'wrap', md: 'nowrap' }}
						>
							<Box flex='1'>
								<Flex alignItems='center' gap={2} flexWrap='wrap' mb={2}>
									<Heading
										id={`credit-${credit.id}`}
										as='h3'
										variant='cardItemTitle'
										fontSize='2xl'
										my={0}
									>
										<LinkOverlay
											as={Button}
											onClick={onClick}
											aria-label={`Edit credit ${title}`}
											color='inherit'
											variant='link'
											textDecoration='none'
											fontFamily='inherit'
											fontSize='inherit'
											_hover={{ textDecoration: 'none' }}
										>
											{title}
										</LinkOverlay>
									</Heading>
									<Badge
										flex='0 0 auto'
										fontSize='sm'
										textTransform='none'
									>{` ${yearString()}`}</Badge>
								</Flex>
								<Box my={0} fontFamily='special'>
									{jobTitle && (
										<WrapWithIcon icon={FiBriefcase} my={0}>
											{jobTitle}
										</WrapWithIcon>
									)}
									{venue ? (
										<WrapWithIcon icon={FiStar} mr={1} my={0}>
											{decodeString(venue)}
										</WrapWithIcon>
									) : (
										false
									)}
									{jobLocation ? (
										<WrapWithIcon icon={FiMapPin} mr={1} my={0}>
											{decodeString(`${jobLocation}`)}
										</WrapWithIcon>
									) : (
										false
									)}
								</Box>
								<InternFellowBadges justifyContent='flex-start' alignItems='center' mt={4} mb={0} />
							</Box>

							<Box flex={{ base: '0 0 100%', md: '0 50%' }}>
								<Stack direction='column' mt={{ base: 4, md: 0 }}>
									{departmentIds?.length || jobIds?.length || skillIds?.length ? (
										<PositionsDisplay item={credit} />
									) : isEditable ? (
										<Wrap justify='right'>
											<Text
												textAlign={{ base: 'left', md: 'right' }}
												maxWidth='250px'
												fontSize='sm'
												lineHeight='short'
											>
												This credit won&apos;t be searchable until you add at least one department
												and a job.
											</Text>
										</Wrap>
									) : (
										false
									)}
								</Stack>
							</Box>
						</Flex>
					</Skeleton>
				</Card>
			</LinkBox>
		</Box>
	);
}

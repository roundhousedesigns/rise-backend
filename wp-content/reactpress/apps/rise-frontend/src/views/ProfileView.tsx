import {
	Avatar,
	Box,
	Button,
	Card,
	Flex,
	Heading,
	HeadingProps,
	Icon,
	Image,
	Link,
	List,
	ListItem,
	SimpleGrid,
	Spacer,
	Stack,
	Tag,
	Text,
	Tooltip,
	useBreakpointValue,
	useColorMode,
	Wrap,
} from '@chakra-ui/react';
import ColorCascadeBox from '@common/ColorCascadeBox';
import LinkWithIcon from '@common/LinkWithIcon';
import ProfileStackItem from '@common/ProfileStackItem';
import StarToggleIcon from '@common/StarToggleIcon';
import WrapWithIcon from '@common/WrapWithIcon';
import ConflictDateRanges from '@components/ConflictDateRanges';
import CreditItem from '@components/CreditItem';
import PersonalIconLinks from '@components/PersonalIconLinks';
import PositionsTagLegend from '@components/PositionsTagLegend';
import ResumePreviewModal from '@components/ResumePreviewModal';
import { Credit, UserProfile, WPItem } from '@lib/classes';
import { getWPItemsFromIds } from '@lib/utils';
import useResumePreview from '@queries/useResumePreview';
import useUserTaxonomies from '@queries/useUserTaxonomies';
import { Key } from 'react';
import { FiExternalLink, FiGlobe, FiMail, FiMap, FiMapPin, FiPhone, FiUser } from 'react-icons/fi';
import ReactPlayer from 'react-player/lazy';
import { Link as RouterLink, useParams } from 'react-router-dom';

interface Props {
	profile: UserProfile;
	allowStar?: boolean;
}

const ProfileHeading = ({ title, ...props }: { title: string } & HeadingProps) => {
	return (
		<Heading as='h1' lineHeight='none' variant='profileName' {...props}>
			{title}
		</Heading>
	);
};

/**
 * @param {UserProfile} profile The user profile data.
 * @returns {JSX.Element} The Props component.
 */
export default function ProfileView({ profile, allowStar = true }: Props): JSX.Element | null {
	const params = useParams();

	const slug = params.slug ? params.slug : '';

	const isLargerThanMd = useBreakpointValue(
		{
			base: false,
			sm: true,
		},
		{ ssr: false }
	);

	const {
		id,
		isOrg,
		orgName,
		image,
		pronouns,
		selfTitle,
		homebase,
		locations,
		website,
		languages,
		socials,
		unions,
		partnerDirectories,
		willTravel,
		willTour,
		email,
		phone,
		resume,
		description,
		mediaVideo1,
		mediaVideo2,
		mediaImage1,
		mediaImage2,
		mediaImage3,
		mediaImage4,
		mediaImage5,
		mediaImage6,
		education,
		conflictRanges,
		credits,
	} = profile || {};

	const { colorMode } = useColorMode();

	// Ensure media videos are unique
	const mediaVideos = Array.from(new Set([mediaVideo1, mediaVideo2].filter(Boolean)));

	const mediaImages = [
		mediaImage1,
		mediaImage2,
		mediaImage3,
		mediaImage4,
		mediaImage5,
		mediaImage6,
	].filter((image) => !!image);

	const creditsSorted = credits
		? credits.sort((a: Credit, b: Credit) => (a.index > b.index ? 1 : -1))
		: [];

	const [
		{ locations: locationTerms, unions: unionTerms, partnerDirectories: partnerDirectoryTerms },
	] = useUserTaxonomies();

	const { attachment } = useResumePreview(resume ? resume : '');

	interface SelectedTermsProps {
		ids: number[];
		terms: WPItem[];
	}

	// Selected term items.
	const SelectedTerms = ({ ids, terms }: SelectedTermsProps) => {
		const items = getWPItemsFromIds(ids, terms);

		return items ? (
			<Flex gap={1} flexWrap='wrap'>
				{items.map((item: WPItem) => (
					<Tag key={item.id}>{item.name}</Tag>
				))}
			</Flex>
		) : null;
	};

	function selectedLinkableTerms({
		ids,
		terms,
	}: {
		ids: number[];
		terms: WPItem[];
	}): (JSX.Element | null)[] {
		return getWPItemsFromIds(ids, terms).map((term: WPItem) => {
			if (term.externalUrl) {
				return (
					<ListItem key={term.id}>
						<Button
							as={Link}
							href={term.externalUrl}
							color='text.dark !important'
							isExternal
							name={`Link to ${term.name}`}
							size='sm'
							m={0}
							colorScheme='orange'
						>
							{term.name}
						</Button>
					</ListItem>
				);
			}

			return null;
		});
	}

	// Build the subtitle string.
	const ProfileSubtitle = ({ ...props }: any) => {
		const SelfTitle = () => {
			return (
				<Text as='span' textDecoration='underline'>
					{selfTitle}
				</Text>
			);
		};

		const HomeBase = () => {
			return (
				<Text as='span' textDecoration='underline'>
					{homebase}
				</Text>
			);
		};

		return (
			<Heading as='h2' size='sm' fontWeight='medium' variant='contentSubtitle' {...props}>
				{selfTitle && homebase && !isOrg ? (
					<>
						<SelfTitle /> based in <HomeBase />
					</>
				) : (
					selfTitle || <HomeBase />
				)}
			</Heading>
		);
	};

	const profileName = isOrg ? orgName : profile.fullName();

	return profile ? (
		<Stack direction='column' flexWrap='nowrap' gap={6}>
			<ProfileStackItem as={Card} p={4} mt={2}>
				<Flex
					gap={6}
					flexWrap={{ base: 'wrap', md: 'nowrap' }}
					justifyContent={{ base: 'center', md: 'flex-start' }}
				>
					<Stack
						direction='column'
						w={isLargerThanMd ? '40%' : 'full'}
						maxW='400px'
						textAlign='center'
						gap={2}
						mt={isLargerThanMd ? 0 : 0}
					>
						<ColorCascadeBox mb={3}>
							{image ? (
								<Image
									src={image}
									alt={`${profileName}'s picture`}
									borderRadius='md'
									loading='eager'
									fit='cover'
									w='full'
									transform='translate(0, 0)'
								/>
							) : (
								<Link as={RouterLink} to='/profile/edit' opacity={0.8} _hover={{ opacity: 1 }}>
									<Tooltip
										role='presentation'
										label='Add a profile image'
										placement='bottom'
										bg={colorMode === 'dark' ? 'gray.700' : 'text.light'}
										hasArrow
									>
										<Avatar size='superLg' src={''} name={''} mt={7} mb={5} mx={4} />
									</Tooltip>
								</Link>
							)}
						</ColorCascadeBox>

						{!isLargerThanMd && (
							<>
								<ProfileHeading title={profileName || ''} pt={0} mr={2} my={0} />
								{!isOrg && pronouns && (
									<Tag
										colorScheme='blue'
										size='md'
										mt={{ base: 2, sm: 'initial' }}
										position='relative'
										bottom={{ base: 0, sm: 1 }}
									>
										{pronouns}
									</Tag>
								)}
							</>
						)}

						<PersonalIconLinks
							socials={socials}
							profileSlug={slug}
							boxSize={10}
							justifyContent='center'
						/>

						{email || phone || website ? (
							<ProfileStackItem>
								<List
									mt={3}
									spacing={1}
									borderRadius='md'
									p={3}
									_dark={{ bg: 'blackAlpha.400' }}
									_light={{ bg: 'blackAlpha.300' }}
								>
									{email ? (
										<ListItem>
											<LinkWithIcon href={`mailto:${email}`} icon={FiMail}>
												{`Email ${isOrg ? orgName : profile.firstName}`}
											</LinkWithIcon>
										</ListItem>
									) : null}
									{phone ? (
										<ListItem>
											<LinkWithIcon href={`tel:${phone}`} icon={FiPhone}>
												{phone}
											</LinkWithIcon>
										</ListItem>
									) : null}
									{website ? (
										<ListItem>
											<LinkWithIcon href={website} target='_blank' icon={FiExternalLink}>
												Visit Website
											</LinkWithIcon>
										</ListItem>
									) : null}
								</List>
							</ProfileStackItem>
						) : null}

						{conflictRanges.length ? (
							<Card pb={0} _dark={{ bg: 'gray.600' }} _light={{ bg: 'gray.200' }}>
								<Box>
									<Heading as='h3' variant='contentTitle'>
										Schedule Conflicts
									</Heading>
									<ConflictDateRanges my={4} conflictRanges={conflictRanges} />
								</Box>
							</Card>
						) : null}
					</Stack>

					{id && allowStar ? (
						<StarToggleIcon
							id={id}
							mx={{ base: 0 }}
							borderRadius='full'
							size='lg'
							pos='absolute'
							top={2}
							right={2}
						/>
					) : (
						false
					)}
					<Stack direction='column' justifyContent='flex-start' gap={6} width='100%' lineHeight={1}>
						<Box>
							<Flex
								justifyContent={{ base: 'center', sm: 'space-between' }}
								w='full'
								flexWrap='wrap'
								alignItems='flex-end'
							>
								{isLargerThanMd && (
									<ProfileHeading title={profileName || ''} pt={4} mr={2} my={0} />
								)}

								{!isOrg && pronouns && (
									<Tag
										colorScheme='blue'
										size='md'
										mt={{ base: 2, sm: 'initial' }}
										position='relative'
										bottom={{ base: 0, sm: 1 }}
									>
										{pronouns}
									</Tag>
								)}
								<Spacer flex={1} />
							</Flex>
							<ProfileSubtitle flex='0 0 100%' w='full' />
						</Box>

						{locations && locations.length > 0 ? (
							<ProfileStackItem title={isOrg ? 'Based in' : 'Works in'}>
								<>
									<WrapWithIcon icon={FiMapPin} mr={2}>
										{locationTerms ? <SelectedTerms ids={locations} terms={locationTerms} /> : null}
									</WrapWithIcon>
									{!isOrg && (
										<WrapWithIcon icon={FiMap} mr={2}>
											<Wrap>
												{willTravel !== undefined && (
													<Tag size='md' colorScheme={willTravel ? 'green' : 'orange'}>
														{willTravel ? 'Will Travel' : 'Local Only'}
													</Tag>
												)}
												{willTour !== undefined && (
													<Tag size='md' colorScheme={willTour ? 'green' : 'orange'}>
														{willTour ? 'Will Tour' : 'No Tours'}
													</Tag>
												)}
											</Wrap>
										</WrapWithIcon>
									)}
								</>
							</ProfileStackItem>
						) : null}

						{unions && unions.length > 0 && unionTerms ? (
							<ProfileStackItem title='Affiliations'>
								<WrapWithIcon icon={FiUser}>
									{SelectedTerms({ ids: unions, terms: unionTerms })}
								</WrapWithIcon>
							</ProfileStackItem>
						) : null}

						{partnerDirectories && partnerDirectories.length > 0 && partnerDirectoryTerms ? (
							<ProfileStackItem title='RISE Partner Directories'>
								<Flex alignItems='center' flexWrap='nowrap' justifyContent='space-between'>
									<Icon as={FiExternalLink} boxSize={4} flex='0 0 auto' />
									<List flex='1' pl={2} spacing={2}>
										{selectedLinkableTerms({
											ids: partnerDirectories,
											terms: partnerDirectoryTerms,
										})}
									</List>
								</Flex>
							</ProfileStackItem>
						) : null}

						{conflictRanges.length && !isLargerThanMd ? (
							<ProfileStackItem title='Conflicts'>
								<ConflictDateRanges my={4} conflictRanges={conflictRanges} />
							</ProfileStackItem>
						) : null}

						{languages ? (
							<ProfileStackItem title='Additional Languages' my={2}>
								<WrapWithIcon icon={FiGlobe} m={0}>
									<Text m={0}>{languages}</Text>
								</WrapWithIcon>
							</ProfileStackItem>
						) : null}

						{resume && attachment?.sourceUrl ? (
							<ProfileStackItem title='Resume'>
								<Flex gap={0} maxW='2xs'>
									<ResumePreviewModal
										resumePreviewSrc={attachment.sourceUrl}
										resumeLink={resume}
										previewIcon={false}
										maxW='full'
									/>
								</Flex>
							</ProfileStackItem>
						) : null}
					</Stack>
				</Flex>
			</ProfileStackItem>

			{credits && credits.length > 0 && !isOrg && (
				<ProfileStackItem centerlineColor='brand.blue' title='Credits'>
					<>
						<Flex justifyContent='flex-end'>
							<PositionsTagLegend mr={4} />
						</Flex>
						<List m={0}>
							{creditsSorted.map((credit: Credit) => (
								<ListItem key={credit.id}>
									<CreditItem credit={credit} />
								</ListItem>
							))}
						</List>
					</>
				</ProfileStackItem>
			)}

			{description && (
				<ProfileStackItem centerlineColor='brand.orange' title='About'>
					<Text whiteSpace='pre-wrap' borderRadius='md'>
						{description.trim()}
					</Text>
				</ProfileStackItem>
			)}

			{education && (
				<ProfileStackItem centerlineColor='brand.green' title='Education + Training'>
					<Text whiteSpace='pre-wrap' borderRadius='md'>
						{education.trim()}
					</Text>
				</ProfileStackItem>
			)}

			{mediaVideos.length > 0 || mediaImages.length > 0 ? (
				<ProfileStackItem centerlineColor='brand.blue' title='Media'>
					<>
						{mediaVideos.length > 0 ? (
							<>
								<Heading as='h3' variant='contentTitle' size='md'>
									Video
								</Heading>
								<SimpleGrid columns={[1, 2]} mt={4} spacing={4}>
									{mediaVideos.map((video: string | undefined) => {
										if (!video) return false;
										return (
											// Videos are unique, so we can just use the string as the key.
											<Box key={video} position='relative' paddingBottom='56.25%'>
												<Box position='absolute' top={0} left={0} width='100%' height='100%'>
													<ReactPlayer url={video} controls width='100%' height='100%' />
												</Box>
											</Box>
										);
									})}
								</SimpleGrid>
							</>
						) : null}
						{mediaImages.length > 0 ? (
							<Box mt={6}>
								<Heading as='h3' variant='contentTitle' size='md'>
									Images
								</Heading>

								<Box w='full' sx={{ columnCount: [1, 2, 3], columnGap: '8px' }}>
									{mediaImages.map((image: string | undefined, index: Key) => (
										<Image
											key={index}
											src={image}
											borderRadius='md'
											fit='cover'
											mb={2}
											alt={`${profileName}'s image`}
										/>
									))}
								</Box>
							</Box>
						) : null}
					</>
				</ProfileStackItem>
			) : null}
		</Stack>
	) : null;
}

import {
    Badge,
    Box,
    Button,
    ButtonGroup,
    Card,
    Flex,
    Heading,
    Link,
    Spacer,
    Stack,
    Tag,
    Text,
    Wrap,
} from '@chakra-ui/react';
import HeadingCenterline from '@common/HeadingCenterline';
import PositionsDisplay from '@common/PositionsDisplay';
import WrapWithIcon from '@common/WrapWithIcon';
import { JobPost } from '@lib/classes';
import useViewer from '@queries/useViewer';
import parse from 'html-react-parser';
import {
    FiCalendar,
    FiDollarSign,
    FiEdit2,
    FiExternalLink,
    FiMail,
    FiMap,
    FiPhone,
    FiUser,
} from 'react-icons/fi';
import { Link as RouterLink } from 'react-router-dom';

interface Props {
	job: JobPost;
}

/**
 * @param {UserProfile} profile The user profile data.
 * @returns {React.JSX.Element} The Props component.
 */
export default function JobPostView({ job }: Props): React.JSX.Element | null {
	const [{ loggedInId }] = useViewer();

	const {
		author,
		title,
		status,
		companyName,
		contactEmail,
		contactName,
		contactPhone,
		companyAddress,
		compensation,
		instructions,
		startDate,
		endDate,
		isInternship,
		isUnion,
		description,
		applicationUrl,
		applicationPhone,
		applicationEmail,
		positions: { departments: departmentIds, jobs: jobIds } = { departments: [], jobs: [] },
		skills: skillIds,
	} = job || {};

	const isAuthor = loggedInId === author;

	const parsedCompanyAddress = companyAddress ? parse(companyAddress) : '';

	const parsedDescription = description ? parse(description) : '';

	const parsedCompensation = compensation ? parse(compensation) : '';

	const parsedInstructions = instructions ? parse(instructions) : '';

	return (
		<Box>
			{isAuthor && (
				<Flex alignItems='center' gap={0} mb={4}>
					<Flex gap={2} alignItems='center'>
						<Badge colorScheme={status === 'pending' ? 'yellow' : 'green'} fontSize='md'>
							{status === 'pending' ? 'Pending review' : 'Published'}
						</Badge>
						<Text textAlign='left' fontSize='2xs' color='gray.500' my={0} lineHeight='shorter'>
							{status === 'pending' ? (
								'You may still edit this posting.'
							) : (
								<>
									<Link href='/contact' color='brand.blue'>
										Contact support
									</Link>{' '}
									to edit this posting.
								</>
							)}
						</Text>
					</Flex>
					<Spacer />

					<ButtonGroup size='sm' w='full' justifyContent='flex-end'>
						{status === 'pending' && (
							<Button
								as={RouterLink}
								to={`/job/edit/${job.id}`}
								leftIcon={<FiEdit2 />}
								colorScheme='blue'
							>
								Edit
							</Button>
						)}
					</ButtonGroup>
				</Flex>
			)}
			<Heading variant='pageTitle' mt={0} mb={1} lineHeight='shorter'>
				{title}{' '}
				<Text as='span' fontSize='lg'>
					at {companyName}
				</Text>
			</Heading>

			<Flex alignItems='flex-end' gap={2}>
				{isUnion && (
					<Tag colorScheme='orange' size='sm'>
						Union
					</Tag>
				)}
				{isInternship && (
					<Tag colorScheme='yellow' size='sm'>
						Internship
					</Tag>
				)}
			</Flex>

			<Stack w='full' spacing={8} mb={0}>
				<Flex gap={4} flexWrap='wrap' w='100%'>
					<Card gap={0} flex='0 0 250px' mb={0}>
						{parsedCompanyAddress ? (
							<Stack gap={2}>
								<WrapWithIcon icon={FiMap} iconProps={{ 'aria-label': 'Company address' }} my={0}>
									<Text whiteSpace='pre-wrap' my={0} lineHeight='short'>
										{parsedCompanyAddress}
									</Text>
								</WrapWithIcon>
								<WrapWithIcon icon={FiCalendar} iconProps={{ 'aria-label': 'Start date' }}>
									<Wrap>
										<Text as='span' m={0}>
											{`Starts on ${startDate}${endDate ? ` - ${endDate}` : ''}`}
										</Text>
									</Wrap>
								</WrapWithIcon>
							</Stack>
						) : null}
					</Card>
					<Card flex='1' mb={0}>
						<Stack gap={2}>
							<WrapWithIcon icon={FiUser} iconProps={{ 'aria-label': 'Contact name' }} my={0}>
								{contactName}
							</WrapWithIcon>

							<WrapWithIcon icon={FiMail} iconProps={{ 'aria-label': 'Contact email' }} my={0}>
								<Link as={RouterLink} to={`mailto:${contactEmail}`} my={0}>
									{contactEmail}
								</Link>
							</WrapWithIcon>

							{contactPhone && (
								<WrapWithIcon icon={FiPhone} iconProps={{ 'aria-label': 'Contact phone' }} my={0}>
									<Link as={RouterLink} to={`tel:${contactPhone}`} my={0}>
										{contactPhone}
									</Link>
								</WrapWithIcon>
							)}

							{parsedCompensation && (
								<WrapWithIcon
									icon={FiDollarSign}
									iconProps={{ 'aria-label': 'Compensation' }}
									my={0}
								>
									<Text whiteSpace='pre-wrap' my={0}>
										{parsedCompensation}
									</Text>
								</WrapWithIcon>
							)}
						</Stack>
					</Card>
				</Flex>

				<Box>
					<HeadingCenterline lineColor='brand.orange'>Job Description</HeadingCenterline>
					{departmentIds?.length || jobIds?.length || skillIds?.length ? (
						<PositionsDisplay item={job} />
					) : null}
					<Box className='wp-post-content'>{parsedDescription}</Box>
				</Box>

				<Box>
					<HeadingCenterline lineColor='brand.blue'>How to apply</HeadingCenterline>

					{parsedInstructions ? <Text>{parsedInstructions}</Text> : null}

					<ButtonGroup colorScheme='blue' mt={4}>
						{applicationUrl ? (
							<Button
								as='a'
								href={applicationUrl}
								leftIcon={<FiExternalLink />}
								size='md'
								target='_blank'
								rel='noopener noreferrer'
							>
								Apply Online
							</Button>
						) : null}

						{applicationPhone ? (
							<Button as='a' href={`tel:${applicationPhone}`} leftIcon={<FiPhone />} size='md'>
								Call to Apply: {applicationPhone}
							</Button>
						) : null}

						{applicationEmail ? (
							<Button as='a' href={`mailto:${applicationEmail}`} leftIcon={<FiMail />} size='md'>
								Email to Apply: {applicationEmail}
							</Button>
						) : null}
					</ButtonGroup>
				</Box>
			</Stack>
		</Box>
	);
}

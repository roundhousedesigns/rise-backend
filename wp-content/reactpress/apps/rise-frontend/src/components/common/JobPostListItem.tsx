import {
	Box,
	Card,
	Flex,
	Heading,
	LinkBox,
	LinkOverlay,
	ListItem,
	ListItemProps,
	Spacer,
	Stack,
	Tag,
	Text,
	Wrap,
} from '@chakra-ui/react';
import PositionsDisplay from '@common/PositionsDisplay';
import { JobPost, WPItem } from '@lib/classes';
import { sortAndCompareArrays } from '@lib/utils';
import useLazyTaxonomyTerms from '@queries/useLazyTaxonomyTerms';
import useTaxonomyTerms from '@queries/useTaxonomyTerms';
import { useEffect, useMemo, useState } from 'react';
import { Link as RouterLink } from 'react-router-dom';

interface JobPostListItemProps {
	job: JobPost;
}

export default function JobPostListItem({
	job,
	...props
}: JobPostListItemProps & ListItemProps): JSX.Element {
	const {
		id,
		title,
		companyName,
		isInternship,
		isPaid,
		isUnion,
		compensation,
		startDate,
		endDate,
		positions: { departments: departmentIds, jobs: jobIds } = { departments: [], jobs: [] },
		skills: skillIds,
	} = job;

	// const datesString = endDate ? `${startDate} - ${endDate}` : `Starts ${startDate}`;

	// Get jobs and skills terms from their IDs
	const [termList, setTermList] = useState<number[]>([]);
	const memoizedTermList = useMemo(() => termList, [termList]);

	// Get departments from their IDs
	const [departments] = useTaxonomyTerms(departmentIds ? departmentIds : []);
	// The term items for each set.
	const [jobs, setJobs] = useState<WPItem[]>([]);
	const [skills, setSkills] = useState<WPItem[]>([]);
	const [getTerms, { data: termData, loading: termsLoading }] = useLazyTaxonomyTerms();

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

	// Set jobs and skills state
	useEffect(() => {
		if (!termData) return;

		const {
			terms: { nodes },
		} = termData;

		const jobTerms = jobIds ? nodes.filter((node: WPItem) => jobIds.includes(node.id)) : [];
		const skillTerms = skillIds ? nodes.filter((node: WPItem) => skillIds.includes(node.id)) : [];

		setJobs(jobTerms);
		setSkills(skillTerms);
	}, [termData, jobIds, skillIds]);

	return (
		<ListItem {...props}>
			<LinkBox aria-labelledby={`job-post-${id}`}>
				<Card variant='listItem' textDecoration='none' mx={0} px={4} py={2}>
					<Flex gap={2} alignItems='center'>
						<Box flex='0 0 33%'>
							<Heading as='h3' id={`job-post-${id}`} fontSize='lg' my={0} mb={0}>
								<LinkOverlay as={RouterLink} to={`/job/${id}`} textDecoration='none'>
									{title}
								</LinkOverlay>
							</Heading>
							<Text fontSize='sm' color='gray.500' lineHeight='normal' my={0}>
								{companyName}
							</Text>
						</Box>

						<Stack fontSize='xs' spacing={1}>
							{compensation ? (
								<Text my={0} lineHeight='short'>
									Compensation: {` ${compensation}`}
								</Text>
							) : null}
							<Text my={0} lineHeight='short'>
								Starts: {startDate}
							</Text>
							<Wrap>
								{isInternship && (
									<Tag colorScheme='yellow' size='xs'>
										Internship
									</Tag>
								)}
								{isPaid && (
									<Tag colorScheme='green' size='xs'>
										Paid
									</Tag>
								)}
								{isUnion && (
									<Tag colorScheme='red' size='xs'>
										Union
									</Tag>
								)}
							</Wrap>
						</Stack>

						<Spacer />

						{departmentIds?.length || jobIds?.length || skillIds?.length ? (
							<PositionsDisplay item={job} showDepartmentBadges={false} />
						) : null}
					</Flex>
				</Card>
			</LinkBox>
		</ListItem>
	);
}

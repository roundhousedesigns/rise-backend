import { Stack } from '@chakra-ui/react';
import WPItemBadgeList from './WPItemBadgeList';
import { WPItem, JobPost, Credit } from '@lib/classes';
import useTaxonomyTerms from '@queries/useTaxonomyTerms';
import useLazyTaxonomyTerms from '@queries/useLazyTaxonomyTerms';
import { useEffect, useMemo, useState } from 'react';
import { sortAndCompareArrays } from '@lib/utils';

interface PositionsDisplayProps {
	item: JobPost | Credit;
}

export default function PositionsDisplay({ item }: PositionsDisplayProps): JSX.Element | null {
	// Extract position IDs from the item
	const departmentIds = item.positions?.departments || [];
	const jobIds = item.positions?.jobs || [];
	const skillIds = item.skills || [];

	// Get departments from their IDs
	const [departments] = useTaxonomyTerms(departmentIds);

	// Get jobs and skills terms from their IDs
	const [termList, setTermList] = useState<number[]>([]);
	const memoizedTermList = useMemo(() => termList, [termList]);

	// The term items for each set.
	const [jobs, setJobs] = useState<WPItem[]>([]);
	const [skills, setSkills] = useState<WPItem[]>([]);

	const [getTerms, { data: termData }] = useLazyTaxonomyTerms();

	// Set the term ID list state
	useEffect(() => {
		if (!jobIds?.length && !skillIds?.length) return;

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

	if (!departments?.length && !jobs?.length && !skills?.length) {
		return null;
	}

	return (
		<Stack direction='column'>
			<WPItemBadgeList items={departments} colorScheme='orange' />
			<WPItemBadgeList items={jobs} colorScheme='blue' />
			<WPItemBadgeList items={skills} colorScheme='green' />
		</Stack>
	);
}

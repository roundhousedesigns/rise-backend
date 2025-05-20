import { List, ListProps, Text } from '@chakra-ui/react';
import JobPostListItem from '@common/JobPostListItem';
import { JobPost } from '@lib/classes';

interface Props {
	jobs: JobPost[];
}

export default function JobPostsList({ jobs, ...props }: Props & ListProps) {
	return jobs.length > 0 ? (
		<List spacing={3} {...props}>
			{jobs.map((job) => (
				<JobPostListItem key={job.id} job={job} />
			))}
		</List>
	) : (
		<Text>No jobs found matching your filters.</Text>
	);
}

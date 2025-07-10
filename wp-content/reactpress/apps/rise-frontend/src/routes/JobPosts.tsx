import { Button, ButtonGroup } from '@chakra-ui/react';
import Shell from '@layout/Shell';
import useFilteredJobPostIds from '@queries/useFilteredJobPostIds';
import useJobPosts from '@queries/useJobPosts';
import useViewer from '@queries/useViewer';
import JobPostsView from '@views/JobPostsView';
import { Link as RouterLink } from 'react-router-dom';

const JobPostButton = () => {
	const [{ loggedInId }] = useViewer();
	const [allJobPostIds] = useFilteredJobPostIds();
	const [jobs] = useJobPosts(allJobPostIds);

	const postedJobs = jobs.filter((job) => job.author === loggedInId);

	return (
		<ButtonGroup>
			<Button as={RouterLink} to='/job/new'>
				Post a Job
			</Button>
			{postedJobs.length > 0 && (
				<Button as={RouterLink} to='/jobs/manage'>
					Manage Posted Jobs
				</Button>
			)}
		</ButtonGroup>
	);
};

export default function JobPosts() {
	return (
		<Shell title='Jobs Board' actions={<JobPostButton />} w='full'>
			<JobPostsView />
		</Shell>
	);
}

import { Button } from '@chakra-ui/react';
import Shell from '@layout/Shell';
import useFilteredJobPostIds from '@queries/useFilteredJobPostIds';
import useJobPosts from '@queries/useJobPosts';
import useViewer from '@queries/useViewer';
import ManageJobPostsView from '@views/ManageJobPostsView';
import { Link as RouterLink } from 'react-router-dom';
export default function ManageJobPosts() {
	const [{ loggedInId }] = useViewer();
	const [allJobPostIds] = useFilteredJobPostIds({ status: ['publish', 'pending'] });
	const [jobs, { loading }] = useJobPosts(allJobPostIds);

	const postedJobs = jobs.filter((job) => job.author === loggedInId);

	const NewJobPostButton = () => (
		<Button as={RouterLink} to='/job/new'>
			Post a Job
		</Button>
	);
	return (
		<Shell title='Manage Job Posts' loading={loading} actions={<NewJobPostButton />}>
			<ManageJobPostsView jobs={postedJobs} />
		</Shell>
	);
}

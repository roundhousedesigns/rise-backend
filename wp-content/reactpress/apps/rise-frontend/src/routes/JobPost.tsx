import Shell from '@layout/Shell';
import useJobPosts from '@queries/useJobPosts';
import JobPostView from '@views/JobPostView';
import { useParams } from 'react-router-dom';

export default function JobPost() {
	const params = useParams();
	const id = params.id ? params.id : '';
	const [job, { loading }] = useJobPosts([parseInt(id)]);

	console.info('job', job);

	return (
		<Shell loading={!!loading}>
			<JobPostView job={job[0]} />
		</Shell>
	);
}

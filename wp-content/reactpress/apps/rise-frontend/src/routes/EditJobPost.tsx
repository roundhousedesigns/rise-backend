import { Box, Divider, Spinner, Text } from '@chakra-ui/react';
import EditJobPostForm from '@components/EditJobPostForm';
import Shell from '@layout/Shell';
import { JobPostOutput } from '@lib/types';
import useJobPosts from '@queries/useJobPosts';
import { useParams } from 'react-router-dom';

interface Position {
	id: number;
	parentId: number | null;
}

interface Skill {
	id: number;
}

export default function EditJobPost() {
	const { id: jobId } = useParams();
	const [jobPosts, { isLoading }] = useJobPosts(jobId ? [parseInt(jobId)] : []);

	// Transform JobPost to JobPostOutput
	const initialData: JobPostOutput | undefined = jobPosts?.[0]
		? {
				id: jobPosts[0].id,
				title: jobPosts[0].title || '',
				companyName: jobPosts[0].companyName,
				companyAddress: jobPosts[0].companyAddress,
				contactName: jobPosts[0].contactName,
				contactEmail: jobPosts[0].contactEmail,
				contactPhone: jobPosts[0].contactPhone,
				startDate: jobPosts[0].startDate,
				endDate: jobPosts[0].endDate,
				instructions: jobPosts[0].instructions,
				compensation: jobPosts[0].compensation,
				applicationUrl: jobPosts[0].applicationUrl,
				applicationPhone: jobPosts[0].applicationPhone,
				applicationEmail: jobPosts[0].applicationEmail,
				description: jobPosts[0].description,
				isPaid: jobPosts[0].isPaid,
				isInternship: jobPosts[0].isInternship,
				isUnion: jobPosts[0].isUnion,
				departments: jobPosts[0].positions.departments.map((d: Position | number) =>
					typeof d === 'object' ? d.id : Number(d)
				),
				jobs: jobPosts[0].positions.jobs.map((j: Position | number) =>
					typeof j === 'object' ? j.id : Number(j)
				),
				skills: jobPosts[0].skills.map((s: Skill | number) =>
					typeof s === 'object' ? s.id : Number(s)
				),
		  }
		: undefined;

	if (isLoading) {
		return <Spinner />;
	}

	return (
		<Shell title={jobId ? 'Edit Job Posting' : 'New Job Posting'}>
			<Text size='md'>
				{jobId
					? `You may edit your job posting while it is still pending publication.`
					: `Create a new job posting to be reviewed and published. You will be able to edit it later, until it is published.`}
			</Text>

			<Divider />

			<Box maxW='3xl' textAlign='left'>
				<EditJobPostForm initialData={initialData} />
			</Box>
		</Shell>
	);
}

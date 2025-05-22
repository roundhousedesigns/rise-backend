import {
	Box,
	Button,
	chakra,
	Flex,
	FormControl,
	FormLabel,
	Heading,
	Input,
	Spinner,
	Stack,
	Text,
	Textarea,
	useToast,
} from '@chakra-ui/react';
import CheckboxButton from '@common/inputs/CheckboxButton';
import ProfileCheckboxGroup from '@common/inputs/ProfileCheckboxGroup';
import RequiredAsterisk from '@common/RequiredAsterisk';
import { WPItem } from '@lib/classes';
import { JobPostOutput } from '@lib/types';
import { sortWPItemsByName } from '@lib/utils';
import useUpdateJobPost from '@mutations/useUpdateJobPost';
import useLazyPositions from '@queries/useLazyPositions';
import useLazyRelatedSkills from '@queries/useLazyRelatedSkills';
import usePositions from '@queries/usePositions';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

// TODO implement recaptcha
// import { executeRecaptcha } from '@hooks/useGoogleReCaptcha';
// import { handleReCaptchaVerify } from '@lib/utils';

interface EditJobFormProps {
	initialData?: JobPostOutput;
}

export default function EditJobForm({ initialData }: EditJobFormProps) {
	const [isSubmitting, setIsSubmitting] = useState(false);
	const toast = useToast();
	const { updateJobPostMutation } = useUpdateJobPost();
	const navigate = useNavigate();

	const [formData, setFormData] = useState<JobPostOutput>({
		title: '',
		companyName: '',
		companyAddress: '',
		contactName: '',
		contactEmail: '',
		contactPhone: '',
		description: '',
		startDate: '',
		endDate: '',
		instructions: '',
		compensation: '',
		applicationUrl: '',
		applicationPhone: '',
		applicationEmail: '',
		isPaid: false,
		isInternship: false,
		isUnion: false,
		departments: [],
		jobs: [],
		skills: [],
	});

	// Add state for departments, jobs, and skills
	const [allDepartments] = usePositions();
	const [getJobs, { loading: jobsLoading }] = useLazyPositions();
	const [jobs, setJobs] = useState<WPItem[]>([]);
	const [getRelatedSkills, { loading: relatedSkillsLoading }] = useLazyRelatedSkills();
	const [skills, setSkills] = useState<WPItem[]>([]);

	// Add effect to load initial data
	useEffect(() => {
		// If we have initial data, load the jobs and skills based on that
		if (initialData) {
			// Access departments directly from initialData
			const departments = initialData.departments || [];
			refetchAndSetJobs(departments).then((jobIds) => {
				if (jobIds.length > 0) {
					refetchAndSetSkills(jobIds);
				}
			});
		}
	}, [initialData]);

	useEffect(() => {
		if (initialData) {
			// Format dates to YYYY-MM-DD for date inputs
			const formattedData = {
				...initialData,
				departments: initialData.departments || [],
				jobs: initialData.jobs || [],
				startDate: initialData.startDate
					? new Date(initialData.startDate).toISOString().split('T')[0]
					: '',
				endDate: initialData.endDate
					? new Date(initialData.endDate).toISOString().split('T')[0]
					: '',
			};
			setFormData(formattedData);
		}
	}, [initialData]);

	// Add functions to handle department, job, and skill changes
	const refetchAndSetJobs = async (departmentIds: number[]) => {
		if (departmentIds.length === 0) {
			setJobs([]);
			return [];
		}

		// Ensure we're passing an array of numbers
		const numericIds = departmentIds
			.map((id) => {
				const numId = Number(id);
				return numId;
			})
			.filter((id) => !isNaN(id));

		if (numericIds.length === 0) {
			setJobs([]);
			return [];
		}

		const jobData = await getJobs({
			variables: { departments: numericIds },
		});

		const jobsByDept = jobData?.data?.jobsByDepartments;

		if (jobsByDept) {
			const sortedJobs = jobsByDept.map((item: WPItem) => new WPItem(item)).sort(sortWPItemsByName);
			setJobs(sortedJobs);
			return jobsByDept.map((j: WPItem) => Number(j.id));
		} else {
			setJobs([]);
			return [];
		}
	};

	const refetchAndSetSkills = async (jobIds: number[]) => {
		if (jobIds.length === 0) {
			setSkills([]);
			return [];
		}

		const skillData = await getRelatedSkills({
			variables: { jobs: jobIds },
		});
		const relatedSkills = skillData?.data?.jobSkills;

		if (relatedSkills) {
			const sortedSkills = relatedSkills
				.map((item: WPItem) => new WPItem(item))
				.sort(sortWPItemsByName);
			setSkills(sortedSkills);
			return relatedSkills.map((s: WPItem) => Number(s.id));
		} else {
			setSkills([]);
			return [];
		}
	};

	const handleDepartmentsChange = (name: string) => (value: string[]) => {
		const termsAsNums = value.map((i) => Number(i));
		setFormData((prev) => ({
			...prev,
			departments: termsAsNums,
		}));

		// Update jobs to align with selected depts
		refetchAndSetJobs(termsAsNums).then((visibleJobs) => {
			setFormData((prev) => {
				const currentJobs = Array.isArray(prev.jobs) ? prev.jobs : [];
				const filteredSelectedJobIds = currentJobs.filter((id: number) => visibleJobs.includes(id));

				// Update skills to align with selected jobs
				refetchAndSetSkills(filteredSelectedJobIds).then((visibleSkills) => {
					setFormData((prev) => {
						const currentSkills = Array.isArray(prev.skills) ? prev.skills : [];
						const filteredSelectedSkillIds = currentSkills.filter((id: number) =>
							visibleSkills.includes(id)
						);
						return {
							...prev,
							skills: filteredSelectedSkillIds,
						};
					});
				});

				return {
					...prev,
					jobs: filteredSelectedJobIds,
				};
			});
		});
	};

	const handleJobsChange = (name: string) => (value: string[]) => {
		const termsAsNums = value.map((i) => Number(i));
		setFormData((prev) => ({
			...prev,
			jobs: termsAsNums,
		}));

		// Update skills to align with selected jobs
		refetchAndSetSkills(termsAsNums).then((visibleSkills) => {
			setFormData((prev) => {
				const currentSkills = Array.isArray(prev.skills) ? prev.skills : [];
				const filteredSelectedSkillIds = currentSkills.filter((id: number) =>
					visibleSkills.includes(id)
				);
				return {
					...prev,
					skills: filteredSelectedSkillIds,
				};
			});
		});
	};

	const handleSkillsChange = (name: string) => (value: string[]) => {
		const termsAsNums = value.map((i) => Number(i));
		setFormData((prev) => ({
			...prev,
			skills: termsAsNums,
		}));
	};

	const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
		const { name, value, type } = e.target;
		if (type === 'checkbox') {
			setFormData((prev) => ({
				...prev,
				[name]: (e.target as HTMLInputElement).checked,
			}));
		} else if (type === 'date') {
			// Ensure date is in YYYY-MM-DD format
			setFormData((prev) => ({
				...prev,
				[name]: value,
			}));
		} else {
			setFormData((prev) => ({
				...prev,
				[name]: value,
			}));
		}
	};

	const handleSubmit = async (e: React.FormEvent) => {
		e.preventDefault();
		setIsSubmitting(true);

		// TODO: Move this to a class method or util function
		// Clean up the data before sending to mutation
		const cleanData = {
			id: formData.id,
			title: formData.title,
			companyName: formData.companyName,
			companyAddress: formData.companyAddress,
			contactName: formData.contactName,
			contactEmail: formData.contactEmail,
			contactPhone: formData.contactPhone,
			startDate: formData.startDate,
			endDate: formData.endDate,
			instructions: formData.instructions,
			compensation: formData.compensation,
			applicationUrl: formData.applicationUrl,
			applicationPhone: formData.applicationPhone,
			applicationEmail: formData.applicationEmail,
			description: formData.description,
			isPaid: formData.isPaid,
			isInternship: formData.isInternship,
			isUnion: formData.isUnion,
			departments: formData.departments,
			jobs: formData.jobs,
			skills: formData.skills,
		};

		updateJobPostMutation(cleanData)
			.then((response) => {
				if (response.data?.updateOrCreateJobPost?.updatedJobPost) {
					toast({
						title: 'Success',
						description: initialData ? 'Job updated successfully' : 'Job created successfully',
						status: 'success',
						duration: 3000,
						isClosable: true,
					});

					if (response.data?.updateOrCreateJobPost?.awaitingPayment) {
						if (response.data?.updateOrCreateJobPost?.wcCheckoutEndpoint) {
							// Hard redirect to the checkout page.
							window.location.href = response.data?.updateOrCreateJobPost?.wcCheckoutEndpoint;
						} else {
							toast({
								title: 'Error',
								description: 'No cart endpoint found. Please contact support.',
								status: 'error',
								duration: 3000,
								isClosable: true,
							});
						}
					} else {
						// Navigate to the job management page
						navigate('/jobs/manage');
					}
				} else {
					throw new Error('Failed to save job post');
				}
			})
			.catch((error) => {
				toast({
					title: 'Error',
					description:
						error instanceof Error ? error.message : 'Something went wrong. Please try again.',
					status: 'error',
					duration: 3000,
					isClosable: true,
				});
			})
			.finally(() => {
				setIsSubmitting(false);
			});
	};

	return (
		<chakra.form onSubmit={handleSubmit} width='100%'>
			<Stack spacing={4} justifyContent='space-between' flexWrap='wrap'>
				<FormControl isRequired>
					<FormLabel>Job Title</FormLabel>
					<Input
						name='title'
						value={formData.title}
						onChange={handleChange}
						placeholder='Enter job title'
					/>
				</FormControl>

				<FormControl isRequired>
					<FormLabel>Company Name</FormLabel>
					<Input
						name='companyName'
						value={formData.companyName}
						onChange={handleChange}
						placeholder='Enter company name'
					/>
				</FormControl>

				<FormControl isRequired>
					<FormLabel>Company Address</FormLabel>
					<Textarea
						name='companyAddress'
						value={formData.companyAddress}
						onChange={handleChange}
						placeholder='Enter company address'
					/>
				</FormControl>

				<FormRow>
					<FormControl isRequired>
						<FormLabel>Contact Name</FormLabel>
						<Input
							name='contactName'
							value={formData.contactName}
							onChange={handleChange}
							placeholder='Enter contact name'
						/>
					</FormControl>

					<FormControl isRequired>
						<FormLabel>Contact Email</FormLabel>
						<Input
							name='contactEmail'
							type='email'
							value={formData.contactEmail}
							onChange={handleChange}
							placeholder='Enter contact email'
						/>
					</FormControl>

					<FormControl>
						<FormLabel>Contact Phone</FormLabel>
						<Input
							name='contactPhone'
							value={formData.contactPhone || ''}
							onChange={handleChange}
							placeholder='Enter contact phone'
						/>
					</FormControl>
				</FormRow>

				<FormRow>
					<FormControl isRequired>
						<FormLabel>Start Date</FormLabel>
						<Input
							name='startDate'
							type='date'
							value={formData.startDate}
							onChange={handleChange}
						/>
					</FormControl>

					<FormControl>
						<FormLabel>End Date (optional)</FormLabel>
						<Input
							name='endDate'
							type='date'
							value={formData.endDate || ''}
							onChange={handleChange}
						/>
					</FormControl>
				</FormRow>

				<FormControl>
					<FormLabel>Compensation</FormLabel>
					<Input
						name='compensation'
						value={formData.compensation || ''}
						onChange={handleChange}
						placeholder='Enter compensation details'
					/>
				</FormControl>

				<FormRow>
					<FormControl isRequired flex='1'>
						<FormLabel>Job Description</FormLabel>
						<Textarea
							name='description'
							value={formData.description || ''}
							onChange={handleChange}
							minHeight='144px'
							placeholder='Enter job description'
						/>
					</FormControl>

					<Stack direction='column' justifyContent='flex-start' flex='0 1 auto'>
						<FormLabel mb={0}>Job Type</FormLabel>
						<CheckboxButton
							name='isPaid'
							value={formData.isPaid ? 'true' : 'false'}
							onChange={handleChange}
							size='sm'
						>
							Paid position
						</CheckboxButton>
						<CheckboxButton
							name='isInternship'
							value={formData.isInternship ? 'true' : 'false'}
							onChange={handleChange}
							size='sm'
						>
							Internship
						</CheckboxButton>
						<CheckboxButton
							name='isUnion'
							value={formData.isUnion ? 'true' : 'false'}
							onChange={handleChange}
							size='sm'
						>
							Union position
						</CheckboxButton>
					</Stack>
				</FormRow>

				<FormRow pt={4} pb={6} px={6} borderRadius='md' _dark={{ bg: 'whiteAlpha.50' }}>
					<FormControl isRequired flex='0 0 100%'>
						<FormLabel>Application Instructions</FormLabel>
						<Textarea
							name='instructions'
							value={formData.instructions}
							onChange={handleChange}
							placeholder='Enter application instructions'
						/>
					</FormControl>

					<FormControl>
						<FormLabel>Application URL</FormLabel>
						<Input
							name='applicationUrl'
							type='url'
							value={formData.applicationUrl || ''}
							onChange={handleChange}
							placeholder='Enter application URL'
						/>
					</FormControl>

					<FormControl>
						<FormLabel>Application Phone</FormLabel>
						<Input
							name='applicationPhone'
							value={formData.applicationPhone || ''}
							onChange={handleChange}
							placeholder='Enter application phone'
						/>
					</FormControl>

					<FormControl>
						<FormLabel>Application Email</FormLabel>
						<Input
							name='applicationEmail'
							type='email'
							value={formData.applicationEmail || ''}
							onChange={handleChange}
							placeholder='Enter application email'
						/>
					</FormControl>
				</FormRow>

				<Stack direction='column' spacing={6} fontSize='md'>
					<Box>
						<Heading as='h4' variant='contentTitle'>
							Department
							<RequiredAsterisk fontSize='md' position='relative' top={-1} />
						</Heading>
						<Text>Select all department(s) for this position.</Text>
						<ProfileCheckboxGroup
							name='departments'
							items={allDepartments}
							checked={formData.departments?.map((item: number) => item.toString()) || []}
							handleChange={handleDepartmentsChange}
						/>
					</Box>

					{formData.departments?.length && !jobsLoading ? (
						<Box>
							<Heading as='h4' variant='contentTitle'>
								Position
								<RequiredAsterisk fontSize='md' position='relative' top={-1} />
							</Heading>
							<Text>Select all positions for this job posting.</Text>
							<ProfileCheckboxGroup
								name='jobs'
								items={jobs}
								checked={formData.jobs?.map((item: number) => item.toString()) || []}
								handleChange={handleJobsChange}
							/>
						</Box>
					) : jobsLoading ? (
						<Spinner />
					) : null}

					{formData.jobs?.length && !relatedSkillsLoading ? (
						<Box>
							<Heading as='h4' variant='contentTitle'>
								Skills
							</Heading>
							<Text>Select any skills required for this position.</Text>
							<ProfileCheckboxGroup
								name='skills'
								items={skills}
								checked={formData.skills?.map((item: number) => item.toString()) || []}
								handleChange={handleSkillsChange}
							/>
						</Box>
					) : relatedSkillsLoading ? (
						<Spinner />
					) : null}
				</Stack>
			</Stack>

			<Button
				type='submit'
				colorScheme='blue'
				isLoading={isSubmitting}
				loadingText='Submitting...'
				my={4}
			>
				{initialData ? 'Update Job' : 'Submit for Review'}
			</Button>
		</chakra.form>
	);
}

const FormRow = ({ children, ...props }: { children: React.ReactNode; [prop: string]: any }) => {
	return (
		<Flex
			gap={4}
			justifyContent='space-between'
			flexWrap='wrap'
			w='full'
			sx={{
				'& > *': {
					flex: '1 0 200px',
					w: 'auto',
				},
			}}
			{...props}
		>
			{children}
		</Flex>
	);
};

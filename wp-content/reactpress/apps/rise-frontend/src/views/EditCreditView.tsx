import {
	Box,
	ButtonGroup,
	Divider,
	Flex,
	Heading,
	Highlight,
	Skeleton,
	Spinner,
	Stack,
	Text,
	useToast,
} from '@chakra-ui/react';
import ProfileCheckboxGroup from '@common/inputs/ProfileCheckboxGroup';
import ProfileRadioGroup from '@common/inputs/ProfileRadioGroup';
import TextInput from '@common/inputs/TextInput';
import TooltipIconButton from '@common/inputs/TooltipIconButton';
import RequiredAsterisk from '@common/RequiredAsterisk';
import { Credit, WPItem } from '@lib/classes';
import { CreditParams } from '@lib/types';
import { sortWPItemsByName } from '@lib/utils';
import useUpdateCredit from '@mutations/useUpdateCredit';
import useLazyPositions from '@queries/useLazyPositions';
import useLazyRelatedSkills from '@queries/useLazyRelatedSkills';
import usePositions from '@queries/usePositions';
import useViewer from '@queries/useViewer';
import { ChangeEvent, memo, useEffect, useState } from 'react';
import { FiCheck, FiX } from 'react-icons/fi';

interface Props {
	credit: Credit;
	onClose: () => void;
}

export default function EditCreditView({ credit, onClose: closeModal }: Props) {
	const [{ loggedInId }] = useViewer();
	const [editCredit, setEditCredit] = useState<CreditParams>(credit);

	const toast = useToast();

	const {
		updateCreditMutation,
		results: { loading: updateCreditLoading },
	} = useUpdateCredit();

	const {
		title,
		jobTitle,
		jobLocation,
		venue,
		workStart,
		workEnd,
		workCurrent,
		intern,
		fellow,
		positions: { departments: selectedDepartmentIds = [], jobs: selectedJobIds = [] },
		skills: selectedSkills,
		isNew,
	} = editCredit;

	const [allDepartments] = usePositions();
	const [getJobs, { loading: jobsLoading }] = useLazyPositions();
	const [jobs, setJobs] = useState<WPItem[]>([]);
	const [getRelatedSkills, { loading: relatedSkillsLoading }] = useLazyRelatedSkills();
	const [skills, setSkills] = useState<WPItem[]>([]);

	const [requirementsMet, setRequirementsMet] = useState<boolean>(false);
	const requiredFields = ['title', 'jobTitle', 'jobLocation', 'venue', 'workStart'];

	const stringifyCredit = JSON.stringify(credit);

	/**
	 * Sync the editCredit state with the credit state.
	 */
	useEffect(() => {
		setEditCredit(credit);
	}, [stringifyCredit]);

	/**
	 * Refetch jobs & skills lists when the selectedDepartmentIds or selectedJobIds change.
	 */
	useEffect(() => {
		refetchAndSetJobs(selectedDepartmentIds);
		refetchAndSetSkills(selectedJobIds);
	}, [selectedDepartmentIds, selectedJobIds]);

	/**
	 * Check that all required fields have been filled.
	 */
	useEffect(() => {
		let allFilled = requiredFields.every((field: string) => {
			if (!!editCredit[field as keyof CreditParams]) return true;

			return false;
		});

		// Ensure a department is set.
		allFilled = allFilled && selectedDepartmentIds.length > 0;

		// Ensure a job is set.
		allFilled = allFilled && selectedJobIds.length > 0;

		setRequirementsMet(allFilled);
	}, [editCredit]);

	/**
	 * Fetches jobs given array of departmentIds, sets jobs
	 *
	 * @returns {Array} returns array of numbers of related/visible jobIds
	 */
	const refetchAndSetJobs = async (departmentIds: number[]) => {
		if (departmentIds.length === 0) {
			setJobs([]);

			return [];
		}

		const jobData = await getJobs({
			variables: { departments: departmentIds },
		});

		const jobsByDept = jobData?.data?.jobsByDepartments;

		if (jobsByDept) {
			setJobs(jobsByDept.map((item: WPItem) => new WPItem(item)).sort(sortWPItemsByName));

			const jobsByDeptIds = jobsByDept.map((j: WPItem) => Number(j.id));

			return jobsByDeptIds;
		} else {
			setJobs([]);

			return [];
		}
	};

	/** Fetches skills given array of jobIds, sets skills
	 *
	 * @returns {Array} returns array of numbers related/visible skillIds
	 */

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
			setSkills(relatedSkills.map((item: WPItem) => new WPItem(item)).sort(sortWPItemsByName));

			const relatedSkillIds = relatedSkills.map((s: WPItem) => Number(s.id));

			return relatedSkillIds;
		} else {
			setSkills([]);

			return [];
		}
	};

	const handleInputChange = (event: ChangeEvent<HTMLInputElement>) => {
		const { name, value } = event.target;

		setEditCredit((prev) => ({
			...prev,
			[name as keyof CreditParams]: value,
		}));
	};

	const dispatchCheckboxTermChange = (name: string, terms: number[]) => {
		if (name === 'departments' || name === 'jobs') {
			setEditCredit((prev) => ({
				...prev,
				positions: {
					...prev.positions,
					[name]: terms,
				},
			}));
		} else if (name === 'skills') {
			setEditCredit((prev) => ({
				...prev,
				skills: terms,
			}));
		}
	};

	const handleDepartmentsChange = (name: string) => async (terms: string[]) => {
		// update depts:
		const termsAsNums = terms.map((i) => Number(i));
		dispatchCheckboxTermChange(name, termsAsNums);

		// update jobs to align with selected depts:
		const visibleJobs = await refetchAndSetJobs(termsAsNums);
		const filteredSelectedJobIds = selectedJobIds.filter((id: number) => visibleJobs.includes(id));
		dispatchCheckboxTermChange('jobs', filteredSelectedJobIds);

		// update skills to align with selected jobs:
		const visibleSkills = await refetchAndSetSkills(filteredSelectedJobIds);
		const filteredSelectedSkillIds =
			selectedSkills?.filter((id: number) => visibleSkills.includes(id)) || [];
		dispatchCheckboxTermChange('skills', filteredSelectedSkillIds);
	};

	const handleJobsChange = (name: string) => async (terms: string[]) => {
		// update jobs
		const termsAsNums = terms.map((i) => Number(i));
		dispatchCheckboxTermChange(name, termsAsNums);

		// update skills to align with selected jobs:
		const visibleSkills = await refetchAndSetSkills(termsAsNums);
		const filteredSelectedSkillIds =
			selectedSkills?.filter((id: number) => visibleSkills.includes(id)) || [];
		dispatchCheckboxTermChange('skills', filteredSelectedSkillIds);
	};

	const handleSkillsChange = (name: string) => (terms: string[]) => {
		// update skills
		const termsAsNums = terms.map((i) => Number(i));
		dispatchCheckboxTermChange(name, termsAsNums);
	};

	const handleRadioInputChange = (name: string) => (value: string) => {
		setEditCredit((prev) => ({
			...prev,
			[name]: value === 'true' ? true : false,
		}));
	};

	const handleSubmit = () => {
		const creditToUpdate = new Credit(editCredit).prepareCreditForGraphQL();

		updateCreditMutation(creditToUpdate, loggedInId)
			.then(() => {
				closeModal();

				toast({
					title: 'Saved!',
					description: 'Your credit has been saved.',
					status: 'success',
					duration: 3000,
					isClosable: true,
					position: 'bottom',
				});
			})
			.catch((err: any) => {
				toast({
					title: 'Oops!',
					description: 'There was an error saving this credit.' + err,
					status: 'error',
					duration: 3000,
					isClosable: true,
					position: 'bottom',
				});

				console.error(err);
			});
	};

	const handleCancel = () => {
		closeModal();
		setEditCredit(credit);
	};

	const EditCreditButtons = memo(() => {
		return (
			<ButtonGroup size='md'>
				<TooltipIconButton
					type='submit'
					isLoading={updateCreditLoading}
					onClick={handleSubmit}
					icon={<FiCheck />}
					label={requirementsMet ? 'Save' : 'Save (please fill in all required fields)'}
					colorScheme='green'
					isDisabled={!requirementsMet || updateCreditLoading}
				/>
				<TooltipIconButton
					icon={<FiX />}
					label='Cancel changes'
					colorScheme='red'
					onClick={handleCancel}
					isDisabled={updateCreditLoading}
				/>
			</ButtonGroup>
		);
	});

	return (
		<Skeleton isLoaded={!!title || !!isNew}>
			<Flex flex='1' justifyContent='space-between' py={5} mb={2}>
				<Heading as='h3' size='lg' lineHeight='base'>
					Edit Credit
				</Heading>
				<EditCreditButtons />
			</Flex>

			<Flex gap={4}>
				<TextInput
					name='title'
					label='Company/Production Name'
					value={title}
					isRequired
					onChange={handleInputChange}
					debounceTime={300}
				/>

				<TextInput
					name='jobTitle'
					label='Job/Position Title'
					isRequired
					value={jobTitle}
					onChange={handleInputChange}
					debounceTime={300}
				/>
			</Flex>

			<Flex justifyContent='space-between' w='full' gap={4} flexWrap='wrap' mt={1}>
				<TextInput
					name='workStart'
					label='Start year'
					isRequired
					value={workStart}
					onChange={handleInputChange}
					flex='1'
					debounceTime={300}
				/>

				<TextInput
					name='workEnd'
					label='End year'
					value={!workCurrent ? workEnd : ''}
					isDisabled={workCurrent}
					onChange={handleInputChange}
					flex='1'
					debounceTime={300}
				/>

				<ProfileRadioGroup
					defaultValue={workCurrent ? 'true' : 'false'}
					name='workCurrent'
					label='Currently working here'
					items={[
						{ label: 'Yes', value: 'true' },
						{ label: 'No', value: 'false' },
					]}
					handleChange={handleRadioInputChange}
				/>
			</Flex>

			<Flex justifyContent='space-between' w='full' gap={4} flexWrap='wrap' mt={1}>
				<TextInput
					name='venue'
					label='Venue'
					value={venue}
					onChange={handleInputChange}
					isRequired
					flex='1'
					debounceTime={300}
				/>

				<TextInput
					name='jobLocation'
					label='Job Location'
					value={jobLocation}
					isRequired
					onChange={handleInputChange}
					flex='1'
					debounceTime={300}
				/>
			</Flex>

			<Flex justifyContent='flex-start' w='full' gap={4} flexWrap='wrap' mt={1}>
				<ProfileRadioGroup
					defaultValue={intern ? 'true' : 'false'}
					name='intern'
					label={`This ${workCurrent ? 'is' : 'was'} an internship`}
					items={[
						{ label: 'Yes', value: 'true' },
						{ label: 'No', value: 'false' },
					]}
					handleChange={handleRadioInputChange}
				/>

				<ProfileRadioGroup
					defaultValue={fellow ? 'true' : 'false'}
					name='fellow'
					label={`This ${workCurrent ? 'is' : 'was'} a fellowship`}
					items={[
						{ label: 'Yes', value: 'true' },
						{ label: 'No', value: 'false' },
					]}
					handleChange={handleRadioInputChange}
				/>
			</Flex>

			<Divider />

			<Stack direction='column' spacing={6} fontSize='md'>
				<Box>
					<Heading as='h4' variant='contentTitle'>
						Department
						<RequiredAsterisk fontSize='md' position='relative' top={-1} />
					</Heading>
					<Text>
						<Highlight
							query={
								selectedDepartmentIds.length > 0
									? 'departments'
									: 'Select all departments you worked under.'
							}
							styles={{
								bg: selectedDepartmentIds.length > 0 ? 'brand.yellow' : 'brand.orange',
								color: selectedDepartmentIds.length > 0 ? 'text.dark' : 'text.light',
							}}
						>
							Select all departments you worked under in this position.
						</Highlight>
					</Text>
					<ProfileCheckboxGroup
						name='departments'
						items={allDepartments}
						checked={
							selectedDepartmentIds
								? selectedDepartmentIds.map((item: number) => item.toString())
								: []
						}
						handleChange={handleDepartmentsChange}
					/>
				</Box>
				{selectedDepartmentIds.length && !jobsLoading ? (
					<Box>
						<Heading as='h4' variant='contentTitle'>
							Position
							<RequiredAsterisk fontSize='md' position='relative' top={-1} />
						</Heading>
						<>
							<Text>
								<Highlight
									query={
										selectedJobIds.length > 0 ? 'jobs' : 'Select all jobs you held on this project.'
									}
									styles={{
										bg: selectedJobIds.length > 0 ? 'brand.yellow' : 'brand.orange',
										color: selectedJobIds.length > 0 ? 'text.dark' : 'text.light',
									}}
								>
									Select all jobs you held on this project.
								</Highlight>
							</Text>
							<ProfileCheckboxGroup
								name='jobs'
								items={jobs}
								checked={
									selectedJobIds ? selectedJobIds.map((item: number) => item.toString()) : []
								}
								handleChange={handleJobsChange}
							/>
						</>
					</Box>
				) : jobsLoading ? (
					<Spinner />
				) : null}

				{selectedJobIds.length && !relatedSkillsLoading ? (
					<Box>
						<Heading as='h4' variant='contentTitle'>
							Skills
						</Heading>
						<>
							<Text>
								<Highlight
									query={
										selectedSkills && selectedSkills.length > 0
											? 'skills'
											: 'Select any skills used on this job.'
									}
									styles={{
										bg:
											selectedSkills && selectedSkills.length > 0 ? 'brand.yellow' : 'brand.orange',
										color: selectedSkills && selectedSkills.length > 0 ? 'text.dark' : 'text.light',
									}}
								>
									Select any skills used on this job.
								</Highlight>
							</Text>
							<ProfileCheckboxGroup
								name='skills'
								items={skills}
								checked={
									selectedSkills ? selectedSkills.map((item: number) => item.toString()) : []
								}
								handleChange={handleSkillsChange}
							/>
						</>
					</Box>
				) : relatedSkillsLoading ? (
					<Spinner />
				) : null}
			</Stack>

			<Flex justifyContent='flex-end' mt={4} mb={0}>
				<EditCreditButtons />
			</Flex>
		</Skeleton>
	);
}

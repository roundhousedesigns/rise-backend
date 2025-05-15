import useSavedSearches from '@/hooks/queries/useSavedSearches';
import { Accordion, Box, chakra, Fade, Flex, Icon, Stack, Text, useToken } from '@chakra-ui/react';
import SearchFilterAccordionItem from '@common/SearchFilterAccordionItem';
import SearchFilterSection from '@common/SearchFilterSection';
import AdditionalSearchFilters from '@components/AdditionalSearchFilters';
import DepartmentsAutocomplete from '@components/DepartmentsAutocomplete';
import SearchFilterDates from '@components/SearchFilterDates';
import SearchFilterDepartment from '@components/SearchFilterDepartment';
import SearchFilterJobs from '@components/SearchFilterJobs';
import SearchFilterName from '@components/SearchFilterName';
import SearchFilterSkills from '@components/SearchFilterSkills';
import { SearchContext } from '@context/SearchContext';
import { FormEvent, useContext } from 'react';
import { FiFolder, FiUser } from 'react-icons/fi';
import SavedSearchItemList from '../components/SavedSearchItemList';

interface Props {
	onSubmit: (e: FormEvent<HTMLFormElement>) => void;
}

export default function SearchWizardView({ onSubmit }: Props) {
	const {
		search: {
			searchWizardActive,
			filters: {
				name,
				filterSet: {
					positions: { departments = [], jobs = [] },
				},
			},
			savedSearch: { id: savedSearchId },
		},
	} = useContext(SearchContext);

	const [savedSearches] = useSavedSearches();

	const [orange] = useToken('colors', ['orange.300']);

	return (
		<Stack
			direction='column'
			justifyContent='space-between'
			height='full'
			pt={searchWizardActive ? 4 : 0}
			transition='padding 250ms ease'
		>
			<Flex gap={0} flexWrap='wrap'>
				<Accordion flex='1 0 300px' allowToggle defaultIndex={savedSearchId ? 0 : undefined}>
					<SearchFilterAccordionItem
						heading={
							<Flex alignItems='center'>
								<Icon
									as={FiFolder}
									fill={savedSearches?.length > 0 ? orange : 'transparent'}
									mr={2}
								/>
								<Text as='span' my={0}>
									Saved Searches
								</Text>
							</Flex>
						}
						isDisabled={!savedSearches || !savedSearches.length}
						headingProps={{ fontSize: 'md' }}
						panelProps={{ mb: 0, px: 3, pb: 4 }}
					>
						<SavedSearchItemList />
					</SearchFilterAccordionItem>
				</Accordion>
				<Accordion flex='1 0 300px' allowToggle defaultIndex={name ? 0 : undefined}>
					<SearchFilterAccordionItem
						heading={
							<Flex alignItems='center'>
								<Icon as={FiUser} mr={2} />
								<Text as='span' my={0}>
									Search by Name
								</Text>
							</Flex>
						}
						headingProps={{ fontSize: 'md' }}
						panelProps={{ mb: 0, mt: -2, px: 3 }}
					>
						<SearchFilterName />
					</SearchFilterAccordionItem>
				</Accordion>
			</Flex>

			<Box
				opacity={name ? 0.2 : 1}
				pointerEvents={name ? 'none' : 'auto'}
				transition='opacity 250ms ease'
			>
				<chakra.form id='search-candidates' onSubmit={onSubmit}>
					<Stack gap={6} mt={searchWizardActive ? 0 : 2} mb={4}>
						<Fade in={!savedSearchId} unmountOnExit>
							<Box>
								<Box maxW='lg'>
									<DepartmentsAutocomplete />
								</Box>
							</Box>
						</Fade>
						<Box>
							<Stack gap={8}>
								<Box>
									<SearchFilterSection id='filterDepartment'>
										<SearchFilterDepartment />
									</SearchFilterSection>
								</Box>
								<Fade in={!!departments.length} unmountOnExit>
									<SearchFilterSection
										id='filterJobs'
										heading='What job(s) are you looking to fill?'
									>
										<SearchFilterJobs />
									</SearchFilterSection>
								</Fade>
								<Fade in={!!departments.length && !!jobs.length} unmountOnExit>
									<SearchFilterSection id='filterSkills' heading='What skills are you looking for?'>
										<SearchFilterSkills />
									</SearchFilterSection>
								</Fade>
								<Fade in={!!departments.length && !!jobs.length} unmountOnExit>
									<SearchFilterSection
										id='filterDates'
										heading='Are you hiring for a particular date?'
									>
										<SearchFilterDates />
									</SearchFilterSection>
								</Fade>
								<Fade in={searchWizardActive && jobs && !!jobs.length} unmountOnExit>
									<SearchFilterSection
										id='filterAdditional'
										heading='And some additional filters to refine your search:'
									>
										<AdditionalSearchFilters />
									</SearchFilterSection>
								</Fade>
							</Stack>
						</Box>
					</Stack>
				</chakra.form>
			</Box>
		</Stack>
	);
}

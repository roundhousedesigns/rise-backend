import { Box, Button, ButtonGroup, Spinner } from '@chakra-ui/react';
import { SearchContext } from '@context/SearchContext';
import Shell from '@layout/Shell';
import useCandidateSearch from '@queries/useCandidateSearch';
import useViewer from '@queries/useViewer';
import SearchView from '@views/SearchView';
import SearchWizardView from '@views/SearchWizardView';
import { isEqual } from 'lodash';
import { FormEvent, useContext, useEffect } from 'react';
import { FiRefreshCcw, FiSearch } from 'react-icons/fi';
import { useNavigate } from 'react-router-dom';

export default function Search() {
	const [{ loggedInId }] = useViewer();

	const {
		search: {
			filters: {
				name,
				filterSet: {
					positions: { departments, jobs },
					skills,
					unions,
					locations,
					experienceLevels,
					genderIdentities,
					racialIdentities,
					personalIdentities,
				},
			},
			results,
			searchWizardActive,
		},
		searchDispatch,
	} = useContext(SearchContext);

	const [getSearchResults, { data: { filteredCandidates } = [], loading: searchResultsLoading }] =
		useCandidateSearch();

	const navigate = useNavigate();

	// Update SearchContext with the new results whenever the query returns.
	useEffect(() => {
		if (isEqual(filteredCandidates, results) || !filteredCandidates) return;

		searchDispatch({
			type: 'SET_RESULTS',
			payload: {
				results: filteredCandidates,
			},
		});
	}, [filteredCandidates]);

	const runSearch = () => {
		// set the positions array to the jobs array if it's not empty, otherwise use the departments array
		const positions = jobs && jobs.length > 0 ? jobs : departments;

		getSearchResults({
			variables: {
				positions,
				skills: skills && skills.length > 0 ? skills : [],
				unions: unions && unions.length > 0 ? unions : [],
				locations: locations && locations.length > 0 ? locations : [],
				experienceLevels: experienceLevels && experienceLevels.length > 0 ? experienceLevels : [],
				genderIdentities: genderIdentities && genderIdentities.length > 0 ? genderIdentities : [],
				racialIdentities: racialIdentities && racialIdentities.length > 0 ? racialIdentities : [],
				personalIdentities:
					personalIdentities && personalIdentities.length > 0 ? personalIdentities : [],
			},
		})
			.then(() => {
				navigate('/results');
			})
			.catch((err) => {
				console.error(err);
			});
	};

	const handleSubmit = (e: FormEvent) => {
		e.preventDefault();
		runSearch();
	};

	const handleSearchReset = () => {
		searchDispatch({
			type: 'RESET_SEARCH_FILTERS',
			payload: {},
		});
	};

	return (
		<Shell
			title='Search RISE'
			description='Search the RISE Directory to find candidates that match your criteria.'
		>
			<SearchView>
				<SearchWizardView onSubmit={handleSubmit} />
				<Box mt={0} py={2}>
					<ButtonGroup w='full' justifyContent='flex-end'>
						<Button
							colorScheme='green'
							onClick={handleSubmit}
							form='search-candidates'
							isDisabled={!searchWizardActive || searchResultsLoading}
							leftIcon={searchResultsLoading ? <Spinner /> : <FiSearch />}
							isLoading={!!searchResultsLoading}
						>
							Search
						</Button>
						<Button
							isDisabled={searchResultsLoading ? true : false}
							colorScheme='orange'
							onClick={handleSearchReset}
							leftIcon={<FiRefreshCcw />}
						>
							Reset
						</Button>
					</ButtonGroup>
				</Box>
			</SearchView>
		</Shell>
	);
}

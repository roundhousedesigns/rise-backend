import { Box, CheckboxGroup, Flex, Spinner } from '@chakra-ui/react';
import CheckboxButton from '@common/inputs/CheckboxButton';
import { WPItem } from '@lib/classes';
import usePositions from '@queries/usePositions';
import { useContext } from 'react';

import { SearchContext } from '@context/SearchContext';

export default function SearchFilterJobs() {
	const {
		search: {
			filters: {
				filterSet: {
					positions: { departments = [], jobs },
				},
			},
		},
		searchDispatch,
	} = useContext(SearchContext);

	const [jobItems, { loading, error }] = usePositions([Number(departments[0])]);

	const handleToggleTerm = (terms: string[]) => {
		searchDispatch({
			type: 'SET_JOBS',
			payload: {
				jobs: terms,
			},
		});
	};

	return (
		<Box>
			{!loading && !error ? (
				<CheckboxGroup value={jobs} onChange={handleToggleTerm} size='sm'>
					<Flex flexWrap='wrap' gap={2}>
						{jobItems.map((term: WPItem) => (
							<CheckboxButton key={term.id} value={term.id.toString()}>
								{term.name}
							</CheckboxButton>
						))}
					</Flex>
				</CheckboxGroup>
			) : loading ? (
				<Spinner />
			) : error ? (
				<>Error</>
			) : (
				<>Nada</>
			)}
		</Box>
	);
}

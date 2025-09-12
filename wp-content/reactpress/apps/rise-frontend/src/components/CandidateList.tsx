import { List, ListProps, Spinner } from '@chakra-ui/react';
import ErrorAlert from '@common/ErrorAlert';
import CandidateListItem from '@components/CandidateListItem';
import useCandidates from '@queries/useCandidates';

interface Props {
	userIds: number[];
	inOrder?: boolean;
	showToggle?: boolean;
	mini?: boolean;
}

export default function CandidateList({
	userIds,
	inOrder,
	showToggle = true,
	mini,
	...props
}: Props & ListProps): React.JSX.Element {
	const [preparedCandidates, { loading, error }] = useCandidates(userIds);

	return (
		<>
			{loading ? (
				<Spinner />
			) : error ? (
				<ErrorAlert message={error.message} />
			) : (
				<List alignItems='left' h='auto' w='full' spacing={4} maxW='3xl' {...props}>
					{inOrder
						? userIds.map((id) => {
								const candidate = preparedCandidates.find((candidate) => candidate.id === id);
								return candidate ? (
									<CandidateListItem
										key={id}
										candidate={candidate}
										mini={mini}
										showToggle={showToggle}
									/>
								) : null;
						  })
						: preparedCandidates.map((candidate) => (
								<CandidateListItem
									key={candidate.id}
									candidate={candidate}
									mini={mini}
									showToggle={showToggle}
								/>
						  ))}
				</List>
			)}
		</>
	);
}

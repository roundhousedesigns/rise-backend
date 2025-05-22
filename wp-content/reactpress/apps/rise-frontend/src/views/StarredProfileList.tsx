import { ListProps } from '@chakra-ui/react';
import CandidateList from '@components/CandidateList';
import useViewer from '@queries/useViewer';

interface Props {
	showToggle?: boolean;
	mini?: boolean;
}

export default function StarredProfileList({
	showToggle,
	mini = false,
	...props
}: Props & ListProps): JSX.Element {
	const [{ starredProfiles }] = useViewer();

	return starredProfiles ? (
		<CandidateList userIds={starredProfiles} spacing={mini ? 1 : 3} mini={mini} {...props} />
	) : (
		<></>
	);
}

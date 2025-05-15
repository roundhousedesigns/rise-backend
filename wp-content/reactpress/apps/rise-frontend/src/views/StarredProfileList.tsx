import { ListProps } from '@chakra-ui/react';
import useViewer from '@queries/useViewer';
import CandidateList from '../components/CandidateList';

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

import { ListProps, Text } from '@chakra-ui/react';
import CandidateList from '@components/CandidateList';
import useViewer from '@queries/useViewer';

interface Props {
	showToggle?: boolean;
	mini?: boolean;
}

export default function FollowedProfileList({
	showToggle = true,
	mini = false,
	...props
}: Props & ListProps): JSX.Element {
	const [{ starredProfiles }] = useViewer();

	return starredProfiles && starredProfiles.length > 0 ? (
		<CandidateList
			userIds={starredProfiles}
			spacing={mini ? 1 : 3}
			mini={mini}
			showToggle={showToggle}
			{...props}
		/>
	) : (
		<Text variant='helperText'>Start following profiles to see them here!</Text>
	);
}

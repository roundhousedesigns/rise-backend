import ProfileNotice from '@common/ProfileNotice';
import useUserProfile from '@queries/useUserProfile';
import useViewer from '@queries/useViewer';

interface Props {
	userId: number;
}

export default function ProfileNotices(): JSX.Element {
	const [{ disableProfile, loggedInId }] = useViewer();
	const [profile] = useUserProfile(loggedInId);

	if (!profile) {
		return <></>;
	}

	const { credits, isOrg } = profile;

	return credits && credits.length < 1 && !isOrg ? (
		<ProfileNotice code='no_credits' status='warning' />
	) : (
		<></>
	);
}

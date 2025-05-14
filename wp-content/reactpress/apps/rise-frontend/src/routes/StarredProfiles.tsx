import InlineIconText from '@components/InlineIconText';
import Shell from '@layout/Shell';
import StarredProfilesView from '@views/StarredProfilesView';
import { FiStar } from 'react-icons/fi';

export default function StarredProfiles() {
	const Description = () => (
		<InlineIconText
			icon={<FiStar />}
			text='Click the star button next to a profile to follow or unfollow it.'
			query='star'
			description='star'
			fontSize='md'
		/>
	);

	return (
		<Shell title='Following' description={<Description />}>
			<StarredProfilesView />
		</Shell>
	);
}

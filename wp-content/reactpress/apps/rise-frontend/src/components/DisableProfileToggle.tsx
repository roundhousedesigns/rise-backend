import { FormControlProps, Highlight, Text, useToast } from '@chakra-ui/react';
import ToggleOptionSwitch from '@common/ToggleOptionSwitch';
import { deleteCookie, setCookie } from '@lib/utils';
import useToggleDisableProfile from '@mutations/useToggleDisableProfile';
import useViewer from '@queries/useViewer';
import { useEffect } from 'react';
import { FiEye, FiEyeOff } from 'react-icons/fi';

interface Props {
	size?: string;
	showLabel?: boolean;
	showHelperText?: boolean;
}

export default function DisableProfileToggle({
	size = 'md',
	showLabel,
	showHelperText,
	...props
}: Props & FormControlProps): JSX.Element {
	const [{ loggedInId, disableProfile }] = useViewer();
	const {
		toggleDisableProfileMutation,
		result: { data, loading },
	} = useToggleDisableProfile();

	const noticeLabel = 'profile_notice_profile_disabled_dismissed';
	const toast = useToast();

	useEffect(() => {
		if (data?.toggleDisableProfile.updatedDisableProfile !== undefined && !loading) {
			const {
				toggleDisableProfile: { updatedDisableProfile },
			} = data || {};

			toast({
				title: 'Updated!',
				description: `Your profile is now ${updatedDisableProfile ? 'private' : 'public'}.`,
				status: 'success',
				duration: 3000,
				isClosable: true,
			});
		}
	}, [data, loading]);

	const handleToggleDisableProfile = () => {
		toggleDisableProfileMutation(loggedInId);

		if (disableProfile === true) setCookie(noticeLabel, 1, 30);
		else deleteCookie(noticeLabel);
	};

	return (
		<ToggleOptionSwitch
			id='disableProfile'
			checked={!!disableProfile}
			callback={handleToggleDisableProfile}
			label={`Private Profile: ${disableProfile ? 'On' : 'Off'}`}
			iconRight={FiEyeOff}
			iconLeft={FiEye}
			size={size}
			loading={loading}
			showLabel={showLabel}
			{...props}
		>
			<>{showHelperText ? <Description disableProfile={!!disableProfile} /> : <></>}</>
		</ToggleOptionSwitch>
	);
}

const Description = ({ disableProfile }: { disableProfile: boolean }) => {
	return (
		<Text
			as='span'
			lineHeight='shorter'
			fontSize='xs'
			_dark={{ color: 'text.light' }}
			_light={{ color: 'text.dark' }}
			opacity={0.8}
		>
			{disableProfile ? (
				<Highlight query={['private', 'hidden']} styles={{ bg: 'brand.yellow', px: 1, mx: 0 }}>
					Your profile is private and you won't appear in searches.
				</Highlight>
			) : (
				<Highlight query={['public']} styles={{ bg: 'brand.yellow', px: 1, mx: 0 }}>
					Your profile is public and you'll appear in searches.
				</Highlight>
			)}
		</Text>
	);
};

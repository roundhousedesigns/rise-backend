import { FormControlProps, Highlight, Text, useToast } from '@chakra-ui/react';
import ToggleOptionSwitch from '@common/ToggleOptionSwitch';
import useToggleIsOrg from '@mutations/useToggleIsOrg';
import useViewer from '@queries/useViewer';
import { useEffect } from 'react';
import { FiBriefcase, FiUser } from 'react-icons/fi';

interface Props {
	size?: string;
	showLabel?: boolean;
	showHelperText?: boolean;
}

export default function IsOrgToggle({
	size = 'md',
	showLabel,
	showHelperText,
	...props
}: Props & FormControlProps): JSX.Element {
	const [{ loggedInId, isOrg }] = useViewer();

	const {
		toggleIsOrgMutation,
		result: { data, loading },
	} = useToggleIsOrg();

	const toast = useToast();

	useEffect(() => {
		if (data?.toggleIsOrg.updatedIsOrg !== undefined && !loading) {
			const {
				toggleIsOrg: { updatedIsOrg },
			} = data || {};

			toast({
				title: 'Updated!',
				description: `Your profile is now set up for ${
					updatedIsOrg ? 'an organization' : 'a person'
				}.`,
				status: 'success',
				duration: 3000,
				isClosable: true,
			});
		}
	}, [data, loading]);

	const handleToggleIsOrg = () => {
		toggleIsOrgMutation(loggedInId);
	};

	return (
		<ToggleOptionSwitch
			id='isOrg'
			checked={!!isOrg}
			callback={handleToggleIsOrg}
			label={`Profile type: ${isOrg ? 'Company' : 'Personal'}`}
			iconRight={FiBriefcase}
			iconLeft={FiUser}
			size={size}
			loading={loading}
			showLabel={showLabel}
			{...props}
		>
			<>{showHelperText ? <Description isOrg={!!isOrg} /> : <></>}</>
		</ToggleOptionSwitch>
	);
}

const Description = ({ isOrg }: { isOrg: boolean }) => {
	return (
		<Text as='span' lineHeight='shorter' fontSize='xs'>
			{isOrg ? (
				<Highlight query={['company']} styles={{ bg: 'brand.yellow', px: 1, mx: 0 }}>
					This is a company profile.
				</Highlight>
			) : (
				<Highlight query={['personal']} styles={{ bg: 'brand.yellow', px: 1, mx: 0 }}>
					This is a personal profile
				</Highlight>
			)}
		</Text>
	);
};

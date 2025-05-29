import { FormControlProps, Highlight, Text, useToast } from '@chakra-ui/react';
import ToggleOptionSwitch from '@common/ToggleOptionSwitch';
import useToggleIsOrg from '@mutations/useToggleIsOrg';
import useViewer from '@queries/useViewer';
import { useEffect } from 'react';
import { FiBriefcase, FiSmile } from 'react-icons/fi';

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
			label={`This profile is for an organization.`}
			iconRight={FiBriefcase}
			iconLeft={FiSmile}
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
				<Highlight query={['organization']} styles={{ bg: 'brand.yellow', px: 1, mx: 0 }}>
					Your profile is set up for an organization.
				</Highlight>
			) : (
				<Highlight query={['person']} styles={{ bg: 'brand.yellow', px: 1, mx: 0 }}>
					Your profile is set up for a person.
				</Highlight>
			)}
		</Text>
	);
};

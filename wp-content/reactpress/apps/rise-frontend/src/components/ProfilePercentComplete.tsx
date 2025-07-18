import { Box, Progress, ProgressProps, Text } from '@chakra-ui/react';
import { useProfileCompletion } from '@hooks/hooks';
import useViewer from '@queries/useViewer';

const ProfilePercentComplete = ({ ...props }: ProgressProps) => {
	const [{ loggedInId }] = useViewer();
	const percentComplete = useProfileCompletion(loggedInId);

	return (
		<Box>
			<Progress
				value={percentComplete}
				colorScheme='red'
				hasStripe
				position='relative'
				borderRadius='md'
				{...props}
			/>
			<Text mt={1} mb={0} fontSize='2xs' textAlign='right' fontStyle='italic' fontFamily='special'>
				{`Profile ${percentComplete}% complete`}
			</Text>
		</Box>
	);
};

export default ProfilePercentComplete;

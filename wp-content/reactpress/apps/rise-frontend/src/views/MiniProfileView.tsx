import {
	Avatar,
	Box,
	Button,
	ButtonGroup,
	Card,
	CardProps,
	Flex,
	Heading,
	Stack,
} from '@chakra-ui/react';
import TooltipIconButton from '@common/inputs/TooltipIconButton';
import ProfilePercentComplete from '@components/ProfilePercentComplete';
import { useProfileCompletion, useProfileUrl } from '@hooks/hooks';
import { UserProfile } from '@lib/classes';
import useViewer from '@queries/useViewer';
import { useState } from 'react';
import { FiEdit3, FiUser } from 'react-icons/fi';
import { Link as RouterLink } from 'react-router-dom';

interface Props {
	profile: UserProfile;
}

/**
 * @param {UserProfile} profile The user profile data.
 * @returns {JSX.Element} The Props component.
 */
export default function MiniProfileView({ profile, ...props }: Props & CardProps): JSX.Element {
	const [{ loggedInSlug, loggedInId, disableProfile }] = useViewer();

	const [isHovered, setIsHovered] = useState(false);

	const handleMouseEnter = () => {
		setIsHovered(true);
	};

	const handleMouseLeave = () => {
		setIsHovered(false);
	};

	const { image } = profile || {};

	const percentComplete = useProfileCompletion(loggedInId);

	const profileUrl = useProfileUrl(loggedInSlug);

	return profile ? (
		<Card
			px={4}
			m={0}
			align='center'
			onMouseEnter={handleMouseEnter}
			onMouseLeave={handleMouseLeave}
			{...props}
		>
			<Flex
				as={Flex}
				mt={2}
				gap={2}
				justifyContent='space-between'
				position='absolute'
				flexWrap='nowrap'
				top={0}
				right={0}
				px={2}
				zIndex={1000}
				width='full'
			>
				<ButtonGroup
					size='xs'
					spacing={1}
					opacity={isHovered ? 1 : 0}
					transition='opacity 200ms ease'
				>
					<TooltipIconButton
						as={RouterLink}
						icon={<FiUser />}
						label={'View profile'}
						to={profileUrl}
						colorScheme='blue'
						my={0}
					>
						View
					</TooltipIconButton>
					<TooltipIconButton
						as={RouterLink}
						icon={<FiEdit3 />}
						label={'Edit profile'}
						to={'/profile/edit'}
						colorScheme='green'
						my={0}
					>
						Edit
					</TooltipIconButton>
				</ButtonGroup>
			</Flex>
			<Stack direction='column' lineHeight={1} w='full'>
				{image ? (
					<Box textAlign='center'>
						<Avatar size='superLg' src={image} name={profile.fullName()} />
					</Box>
				) : null}
				<Box
					flexWrap='wrap'
					justifyContent={{ base: 'center', md: 'flex-start' }}
					alignItems='center'
				>
					<Flex justifyContent='center' flexWrap='wrap' gap={2}>
						<Heading size='md' m={0} fontWeight='bold' lineHeight='none'>
							{profile.fullName()}
						</Heading>
					</Flex>
				</Box>

				{percentComplete > 30 || disableProfile ? (
					<Box>{percentComplete < 100 ? <ProfilePercentComplete colorScheme='blue' /> : null}</Box>
				) : (
					<Button
						as={RouterLink}
						leftIcon={<FiEdit3 />}
						to={'/profile/edit'}
						colorScheme='orange'
						my={2}
					>
						Create Your Profile
					</Button>
				)}
			</Stack>
		</Card>
	) : (
		<></>
	);
}

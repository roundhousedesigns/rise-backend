import { Flex, FlexProps } from '@chakra-ui/react';
import ShareButton from '@common/ShareButton';
import SocialIcon from '@common/SocialIcon';
import XIcon from '@common/icons/X';
import { useProfileUrl } from '@hooks/hooks';
import { PersonalLinks } from '@lib/classes';
import { FiFacebook, FiInstagram, FiLinkedin } from 'react-icons/fi';

interface Props {
	socials: PersonalLinks;
	profileSlug: string;
	boxSize?: number;
}

export default function PersonalIconLinks({
	socials,
	profileSlug,
	boxSize = 12,
	...props
}: Props & FlexProps): JSX.Element {
	const { facebook, twitter, instagram, linkedin } = socials;

	const profileUrl = useProfileUrl(profileSlug);

	return (
		<Flex gap={2} {...props}>
			{linkedin && (
				<SocialIcon
					label='LinkedIn'
					name='linkedin'
					value={linkedin}
					icon={<FiLinkedin />}
					boxSize={boxSize}
				/>
			)}

			{facebook && (
				<SocialIcon
					label='Facebook'
					name='facebook'
					value={facebook}
					icon={<FiFacebook />}
					boxSize={boxSize}
				/>
			)}

			{twitter && (
				<SocialIcon
					label='X/Twitter'
					name='twitter'
					value={twitter}
					icon={<XIcon />}
					boxSize={boxSize}
				/>
			)}

			{instagram && (
				<SocialIcon
					label='Instagram'
					name='instagram'
					value={instagram}
					icon={<FiInstagram />}
					boxSize={boxSize}
				/>
			)}

			<ShareButton url={profileUrl} boxSize={boxSize} borderRadius='full' />
		</Flex>
	);
}

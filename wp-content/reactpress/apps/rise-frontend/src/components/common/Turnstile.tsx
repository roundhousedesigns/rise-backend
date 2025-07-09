import { Text } from '@chakra-ui/react';
import { Turnstile } from '@marsidev/react-turnstile';

interface Props {
	onSuccess: () => void;
	onError: () => void;
	onExpire: () => void;
}

export default function TurnstileComponent({ onSuccess, onError, onExpire }: Props) {
	const { VITE_TURNSTILE_SITE_KEY, VITE_DEV_MODE } = import.meta.env;

	const devMode = VITE_DEV_MODE === 'true' || import.meta.env.MODE === '1';

	if (devMode) {
		return <Text color='brand.orange'>&mdash; Turnstile disabled in dev mode &mdash;</Text>;
	}

	return (
		<Turnstile
			siteKey={VITE_TURNSTILE_SITE_KEY}
			onSuccess={onSuccess}
			onError={onError}
			onExpire={onExpire}
		/>
	);
}

import { Turnstile } from '@marsidev/react-turnstile';

interface Props {
	onSuccess: () => void;
	onError: () => void;
	onExpire: () => void;
}

export default function TurnstileComponent({ onSuccess, onError, onExpire }: Props) {
	const { VITE_TURNSTILE_SITE_KEY, VITE_DEV_MODE } = import.meta.env;

	const devMode = VITE_DEV_MODE === 'true' || import.meta.env.MODE === '1';

	console.info(devMode);

	if (devMode) {
		return <div>Cloudflare Turnstile</div>;
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

import { Turnstile } from '@marsidev/react-turnstile';

interface Props {
	onSuccess: () => void;
	onError: () => void;
	onExpire: () => void;
}

export default function TurnstileComponent({ onSuccess, onError, onExpire }: Props) {
	const { VITE_TURNSTILE_SITE_KEY, VITE_DEV_MODE } = import.meta.env;

	if (VITE_DEV_MODE) {
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

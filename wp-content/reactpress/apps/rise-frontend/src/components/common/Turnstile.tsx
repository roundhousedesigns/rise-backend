import { Turnstile as TurnstileComponent } from '@marsidev/react-turnstile';

interface Props {
	onSuccess: () => void;
	onError: () => void;
	onExpire: () => void;
}

export default function Turnstile({ onSuccess, onError, onExpire }: Props): JSX.Element | null {
	const { VITE_TURNSTILE_SITE_KEY, VITE_DEV_MODE } = import.meta.env;

	const devMode = VITE_DEV_MODE === 'true' || import.meta.env.MODE === '1';

	if (devMode) {
		return null;
	}

	return (
		<TurnstileComponent
			siteKey={VITE_TURNSTILE_SITE_KEY}
			onSuccess={onSuccess}
			onError={onError}
			onExpire={onExpire}
		/>
	);
}

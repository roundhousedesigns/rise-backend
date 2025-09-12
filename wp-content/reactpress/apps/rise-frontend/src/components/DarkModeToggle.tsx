import { FormControlProps, Highlight, Text, useColorMode } from '@chakra-ui/react';
import ToggleOptionSwitch from '@common/ToggleOptionSwitch';
import { FiMoon, FiSun } from 'react-icons/fi';

interface Props {
	size?: string;
	showLabel?: boolean;
	showHelperText?: boolean;
}

export default function DarkModeToggle({
	size = 'md',
	showLabel = true,
	showHelperText = true,
	...props
}: Props & FormControlProps): React.JSX.Element {
	const { colorMode, toggleColorMode } = useColorMode();

	return (
		<ToggleOptionSwitch
			id='darkMode'
			aria-label={`Switch to ${colorMode === 'dark' ? 'light' : 'dark'} mode`}
			checked={colorMode === 'dark'}
			callback={toggleColorMode}
			label='Dark/Light Theme'
			title={colorMode === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'}
			iconLeft={FiSun}
			iconRight={FiMoon}
			size={size}
			showLabel={showLabel}
			{...props}
		>
			{showHelperText ? <Description colorMode={colorMode} /> : ''}
		</ToggleOptionSwitch>
	);
}

const Description = ({ colorMode }: { colorMode: string }) => {
	const text = colorMode === 'dark' ? 'Dark mode' : 'Light mode';

	return (
		<Text
			as='span'
			fontSize='xs'
			_dark={{ color: 'text.light' }}
			_light={{ color: 'text.dark' }}
			opacity={0.8}
		>
			<Highlight query={[colorMode]} styles={{ bg: 'brand.yellow', px: 1 }}>
				{text}
			</Highlight>
		</Text>
	);
};

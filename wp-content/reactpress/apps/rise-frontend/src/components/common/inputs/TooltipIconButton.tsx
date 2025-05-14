import {
	Box,
	IconButton,
	Tooltip,
	TooltipProps,
	useBreakpointValue,
	useColorMode,
} from '@chakra-ui/react';
import { JSXElementConstructor, ReactElement, useState } from 'react';

interface Props {
	icon: ReactElement<any, string | JSXElementConstructor<any>>;
	label: string;
	tooltipProps?: TooltipProps;
	isDisabled?: boolean;
	[prop: string]: any;
}

export default function TooltipIconButton({
	icon,
	label,
	tooltipProps,
	isDisabled,
	...props
}: Props) {
	const [hovered, setHovered] = useState<boolean>(false);

	const { colorMode } = useColorMode();
	const isLargerThanMd = useBreakpointValue(
		{
			base: false,
			md: true,
		},
		{ ssr: false } // TODO: Do we need this?
	);

	return (
		<Box onMouseEnter={() => setHovered(true)} onMouseLeave={() => setHovered(false)}>
			<Tooltip
				label={label}
				placement='bottom'
				aria-hidden={true}
				role='presentation'
				border='none'
				bg={colorMode === 'dark' ? 'gray.700' : 'text.light'}
				isOpen={!!hovered}
				isDisabled={!isLargerThanMd}
				hasArrow
				{...tooltipProps}
			>
				<IconButton
					icon={icon}
					variant='solid'
					aria-label={label}
					pointerEvents={isDisabled ? 'none' : 'auto'}
					{...props}
				/>
			</Tooltip>
		</Box>
	);
}

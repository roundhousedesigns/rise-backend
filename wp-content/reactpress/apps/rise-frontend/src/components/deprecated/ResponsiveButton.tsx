/**
 * @deprecated v1.1.9
 */
import { Button, ButtonProps, IconButton, forwardRef, useBreakpointValue } from '@chakra-ui/react';
import { JSXElementConstructor, ReactElement, ReactNode } from 'react';

interface Props extends ButtonProps {
	label: string;
	icon: ReactElement<any, string | JSXElementConstructor<any>>;
	variant?: string;
	children: ReactNode;
}

const ResponsiveButton = forwardRef<Props, 'div'>(
	({ label, icon, variant, children, ...props }, ref) => {
		const isFullSize = useBreakpointValue(
			{
				base: false,
				md: true,
			},
			{ ssr: false } // TODO: Do we need this?
		);

		return isFullSize ? (
			<Button
				aria-label={label}
				variant={variant}
				title={label}
				leftIcon={icon}
				ref={ref}
				{...props}
			>
				{children}
			</Button>
		) : (
			<IconButton aria-label={label} variant={variant} title={label} icon={icon} {...props} />
		);
	}
);

export default ResponsiveButton;

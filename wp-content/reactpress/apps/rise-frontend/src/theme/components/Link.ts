import { defineStyle, defineStyleConfig } from '@chakra-ui/react';

const baseStyle = defineStyle({
	my: 2,
	color: 'blue.500',
	textDecoration: 'underline',
	transition: 'all 100ms ease-out',
	textDecorationColor: 'transparent',
	_dark: {
		color: 'blue.400',
	},
	_focus: {
		outline: 'none',
	},
	_hover: {
		textDecorationColor: 'initial',
	},
});

const variants = {
	dotted: defineStyle({
		textDecoration: 'none',
		borderBottomColor: 'auto',
		borderBottomStyle: 'dotted',
		borderBottomWidth: '1.5px',
		transition: 'borderBottomStyle 100ms ease-out',
		lineHeight: 'none',
		_hover: {
			textDecoration: 'none',
			borderBottomStyle: 'solid',
			borderBottomWidth: '1.5px',
		},
	}),
};

export default defineStyleConfig({
	baseStyle,
	variants,
});

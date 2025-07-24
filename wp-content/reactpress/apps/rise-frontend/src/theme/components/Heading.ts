import { defineStyle, defineStyleConfig } from '@chakra-ui/react';

const baseStyle = defineStyle({
	mb: 2,
	fontWeight: 'normal',
	fontFamily: 'body',
});

const variants = {
	pageTitle: defineStyle({
		fontSize: { base: '3xl', md: '5xl' },
	}),
	pageSubtitle: defineStyle({
		fontSize: { base: '2xl', md: '3xl' },
	}),
	contentTitle: defineStyle({
		fontSize: '2xl',
		fontFamily: 'special',
		pb: 0,
	}),
	contentSubtitle: defineStyle({
		fontSize: 'lg',
		fontFamily: 'special',
		mt: 0,
		pb: 0,
	}),
	profileName: defineStyle({
		fontWeight: 'bold',
		lineHeight: 'none',
		mt: 0,
		pb: 0,
	}),
	cardItemTitle: defineStyle({
		fontWeight: 'normal',
		fontFamily: 'body',
		fontSize: '2xl',
	}),
	fieldSectionTitle: defineStyle({
		fontSize: '3xl',
		pb: 0,
	}),
	centerline: defineStyle({
		size: 'xl',
		_light: {
			bg: 'bg.light',
		},
		_dark: {
			bg: 'bg.dark',
			color: 'text.light',
		},
		display: 'inline',
		lineHeight: 'none',
		zIndex: '2',
		pr: 2,
	}),
	searchFilterTitle: defineStyle({
		fontSize: '3xl',
		mb: 6,
		w: 'full',
		borderBottom: '2px',
		borderColor: 'gray.600',
	}),
};

export default defineStyleConfig({
	baseStyle,
	variants,
});

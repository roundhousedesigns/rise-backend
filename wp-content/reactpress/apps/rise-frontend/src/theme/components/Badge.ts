import { defineStyle, defineStyleConfig } from '@chakra-ui/react';

const baseStyle = defineStyle({
	borderRadius: 'md',
	fontFamily: 'heading',
	textTransform: 'none',
	px: 2,
	py: 1,
});

export default defineStyleConfig({
	baseStyle,
});

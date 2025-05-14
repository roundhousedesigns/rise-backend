import { ReactNode } from 'react';

interface Props {
	children: ReactNode;
}

export default function SearchView({ children }: Props) {
	return <>{children}</>;
}

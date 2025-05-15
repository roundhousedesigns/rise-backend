import { Box, BoxProps, List, ListItem, Spinner } from '@chakra-ui/react';
import ErrorAlert from '@common/ErrorAlert';
import CandidateItem from '@components/CandidateItem';
import { Candidate } from '@lib/classes';
import useCandidates from '@queries/useCandidates';
import useViewer from '@queries/useViewer';
import { AnimatePresence, motion } from 'framer-motion';
import { isEqual } from 'lodash';
import { useEffect, useMemo, useRef } from 'react';

interface Props {
	showToggle?: boolean;
	mini?: boolean;
}

export default function StarredProfileList({
	showToggle,
	mini = false,
	...props
}: Props & BoxProps): JSX.Element {
	const [{ starredProfiles }] = useViewer();
	const [profiles, { error, loading }] = useCandidates(starredProfiles ? starredProfiles : []);

	const profilesRef = useRef<number[] | undefined>(starredProfiles);

	useEffect(() => {
		if (!isEqual(profilesRef.current, starredProfiles)) {
			profilesRef.current = starredProfiles;
		}
	}, [starredProfiles]);

	const renderedProfiles = useMemo(() => {
		return profilesRef.current?.map((id: number) => {
			const profile = profiles?.find((profile: Candidate) => profile.id === id);

			if (!profile) return null;

			return (
				<ListItem
					key={id}
					as={motion.li}
					initial={{ opacity: 0 }} // Initial state before animation
					animate={{ opacity: 1 }} // Animation state
					exit={{ opacity: 0 }} // Exit state
				>
					<CandidateItem candidate={profile} showToggle={showToggle} mini={mini} />
				</ListItem>
			);
		});
	}, [profiles]);

	return (
		<Box {...props}>
			{!error && !loading ? (
				<List alignItems='left' h='auto' mt={2} w='full' spacing={4}>
					<AnimatePresence>{renderedProfiles}</AnimatePresence>
				</List>
			) : loading ? (
				<Spinner />
			) : error ? (
				<ErrorAlert message={error.message} />
			) : (
				<></>
			)}
		</Box>
	);
}

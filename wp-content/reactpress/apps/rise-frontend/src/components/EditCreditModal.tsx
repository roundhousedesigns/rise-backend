import { Modal, ModalBody, ModalContent, ModalOverlay } from '@chakra-ui/react';
import { Credit } from '@lib/classes';
import useUserProfile from '@queries/useUserProfile';
import useViewer from '@queries/useViewer';
import EditCreditView from '@views/EditCreditView';

interface Props {
	isOpen: boolean;
	onClose: () => void;
	creditId: string;
}

export default function EditCreditModal({ isOpen, onClose, creditId }: Props): React.JSX.Element {
	const [{ loggedInId }] = useViewer();
	const [profile] = useUserProfile(loggedInId);
	const credit =
		profile?.credits?.find((credit) => credit.id === creditId) ||
		new Credit({
			id: creditId,
			index: 0,
			positions: { departments: [], jobs: [] },
			isNew: true,
		});

	return (
		<Modal isOpen={isOpen} onClose={onClose} scrollBehavior='outside' size='3xl'>
			<ModalOverlay />F
			<ModalContent>
				<ModalBody px={8} pb={4}>
					<EditCreditView credit={credit} onClose={onClose} />
				</ModalBody>
			</ModalContent>
		</Modal>
	);
}

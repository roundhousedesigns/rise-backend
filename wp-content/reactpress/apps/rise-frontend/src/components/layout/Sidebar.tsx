import {
	Box,
	BoxProps,
	Button,
	Circle,
	Flex,
	Heading,
	Icon,
	IconButton,
	List,
	ListItem,
	Popover,
	PopoverArrow,
	PopoverBody,
	PopoverCloseButton,
	PopoverContent,
	PopoverHeader,
	PopoverTrigger,
	Portal,
	Text,
	useColorMode,
	useToken,
} from '@chakra-ui/react';
import SidebarMenuItem from '@common/inputs/SidebarMenuItem';
import DarkModeToggle from '@components/DarkModeToggle';
import ProfileNotificationItem from '@components/ProfileNotificationItem';
import { SearchContext } from '@context/SearchContext';
import useDismissProfileNotifications from '@mutations/useDismissProfileNotifications';
import useLogout from '@mutations/useLogout';
import useMarkProfileNotificationsAsRead from '@mutations/useMarkProfileNotificationsAsRead';
import useProfileNotifications from '@queries/useProfileNotifications';
import useSavedSearches from '@queries/useSavedSearches';
import useViewer from '@queries/useViewer';
import { AnimatePresence, motion } from 'framer-motion';
import { ReactNode, useContext, useEffect, useState } from 'react';
import {
	FiBell,
	FiChevronsLeft,
	FiFolder,
	FiHome,
	FiLogOut,
	FiSearch,
	FiSettings,
	FiStar,
	FiUser,
} from 'react-icons/fi';
import { useLocation } from 'react-router-dom';

interface SidebarMenuItemProps {
	icon?: ReactNode;
	target: string | (() => void);
	label: string | ReactNode;
	isActive?: boolean;
	isDisabled?: boolean;
	isExpanded?: boolean;
}

interface SidebarProps extends BoxProps {
	sidebarExpanded: boolean;
	setSidebarExpanded: (expanded: boolean) => void;
}

export default function Sidebar({ sidebarExpanded, setSidebarExpanded, ...props }: SidebarProps) {
	const [{ loggedInId, loggedInSlug, starredProfiles }] = useViewer();
	const [savedSearches] = useSavedSearches();
	const { markProfileNotificationsAsReadMutation } = useMarkProfileNotificationsAsRead();
	const { dismissProfileNotificationsMutation } = useDismissProfileNotifications();
	const { colorMode } = useColorMode();

	const [popoverArrowBgColor, setPopoverArrowBgColor] = useState<string>('');
	const [popoverLightGray, popoverDarkGray] = useToken('colors', ['gray.100', 'gray.600']);

	const {
		search: { results },
	} = useContext(SearchContext);

	const location = useLocation();

	const [{ unread, read }] = useProfileNotifications(loggedInId);

	const { logoutMutation } = useLogout();

	// HACK Set the popover arrow background color based on the color mode
	useEffect(() => {
		setPopoverArrowBgColor(colorMode === 'dark' ? popoverDarkGray : popoverLightGray);
	}, [colorMode]);

	const handleLogout = () => {
		logoutMutation().then(() => {
			localStorage.clear();
			window.location.reload();
		});
	};

	// The sidebar menu items are defined here.
	const menuItems: SidebarMenuItemProps[] = [
		{ icon: <Icon as={FiHome} />, target: `/`, label: 'Dashboard' },
		{ icon: <Icon as={FiUser} />, target: `/profile/${loggedInSlug}`, label: 'Profile' },
		{ icon: <Icon as={FiSearch} />, target: '/search', label: 'Search' },
		{
			icon: (
				<Box w='full' textAlign='center' flex='0' pos='relative' right={0.5} mr={-1}>
					<Flex
						my={2.5}
						mx={0}
						borderRadius='full'
						fontSize='2xs'
						bg='brand.orange'
						w='20px'
						h='20px'
						py={0.25}
						justifyContent='center'
						alignItems='center'
					>
						{results.length}
					</Flex>
				</Box>
			),
			target: '/results',
			label: (
				<Text
					as='span'
					overflow='hidden'
					w='200px'
					visibility={sidebarExpanded ? 'visible' : 'hidden'}
					pos={sidebarExpanded ? 'relative' : 'absolute'}
				>
					{results.length === 1 ? 'Result' : 'Results'}
				</Text>
			),
			isDisabled: results.length === 0,
		},
		{
			icon: (
				<Icon
					as={FiStar}
					fill={starredProfiles && starredProfiles.length > 0 ? 'brand.orange' : 'transparent'}
				/>
			),
			target: '/following',
			label: 'Following',
		},
		{
			icon: (
				<Icon as={FiFolder} fill={savedSearches?.length > 0 ? 'brand.orange' : 'transparent'} />
			),
			target: '/searches',
			label: 'Searches',
		},
		// { icon: <Icon as={FiBriefcase} />, target: '/jobs', label: 'Jobs' }
		{
			icon: <Icon as={FiSettings} />,
			target: '/settings',
			label: 'Settings',
		},
		{
			icon: <Icon as={FiLogOut} />,
			target: () => handleLogout(),
			label: 'Logout',
		},
	];

	const markAllNotificationsAsRead = () => {
		markProfileNotificationsAsReadMutation(
			unread.map((notification) => {
				return notification.id;
			})
		);
	};

	const deleteAllNotifications = () => {
		const allNotifications = [...unread, ...read];

		dismissProfileNotificationsMutation(
			allNotifications.map((notification) => {
				return notification.id;
			})
		);
	};

	// The sidebar is only shown if the user is logged in.
	return loggedInId ? (
		<Box
			id='sidebar'
			minH='100%'
			pt={0}
			pb={0}
			_light={{ bg: 'gray.600', color: 'text.dark' }}
			_dark={{ bg: 'gray.800', color: 'text.light' }}
			transition='all 0.3s ease'
			pos='relative'
			overflow={sidebarExpanded ? 'unset' : 'hidden'}
			{...props}
		>
			<Flex
				w='full'
				mt={0}
				ml={0}
				flexDirection='column'
				flexWrap='nowrap'
				alignItems='flex-start'
				justifyContent='flex-start'
				borderRight='1px solid'
				mr={sidebarExpanded ? 14 : 0}
				transition='all 0.3s ease'
				_light={{ borderColor: 'text.dark' }}
				_dark={{ borderColor: 'gray.800' }}
			>
				<Popover isLazy placement='bottom-end'>
					<PopoverTrigger>
						<IconButton
							position='relative'
							left='0.4rem'
							aria-label='Notifications'
							mx={0}
							my={4}
							p={0}
							variant={unread.length + read.length > 0 ? 'solid' : 'ghost'}
							icon={
								<>
									<Icon as={FiBell} boxSize={4} p={0} m={0} />
									{unread.length > 0 && (
										<Circle
											pos='absolute'
											bottom={-1}
											right={-1}
											size={4}
											textAlign='center'
											bg='orange.300'
											color='white'
											fontSize='2xs'
										>
											{unread.length}
										</Circle>
									)}
								</>
							}
							colorScheme={
								(unread && unread.length > 0) || (read && read.length > 0) ? 'yellow' : 'gray'
							}
							tabIndex={0}
							size='sm'
							transition='all 0.3s ease'
						/>
					</PopoverTrigger>
					<Portal>
						<PopoverContent
							_dark={{ bg: popoverDarkGray, color: 'text.light' }}
							_light={{ bg: popoverLightGray, color: 'text.dark' }}
							boxShadow='1px 1px 1px 1px rgba(0, 0, 0, 0.4)'
						>
							<PopoverArrow
								bg={popoverArrowBgColor}
								boxShadow={`-1px -1px 0px 0 ${popoverArrowBgColor}`}
							/>
							<PopoverCloseButton />
							<PopoverHeader fontFamily='special'>
								<Flex justifyContent='space-between' alignItems='center' w='full'>
									<Heading variant='contentSubtitle' my={0}>
										Alerts
									</Heading>
									{unread.length > 0 && (
										<Button onClick={markAllNotificationsAsRead} size='xs' colorScheme='yellow'>
											Mark all as read
										</Button>
									)}
									{read.length > 0 && (
										<Button onClick={deleteAllNotifications} size='xs' colorScheme='yellow'>
											Delete all
										</Button>
									)}
								</Flex>
							</PopoverHeader>
							<PopoverBody>
								{unread.length === 0 && read.length === 0 && (
									<Text fontSize='xs' my={0} fontStyle='italic'>
										You're all caught up!
									</Text>
								)}

								<AnimatePresence>
									<List>
										{unread.map((notification) => (
											<ListItem
												key={notification.id}
												as={motion.li}
												initial={{ opacity: 1 }}
												animate={{ opacity: 1 }}
												exit={{ opacity: 0 }}
											>
												<ProfileNotificationItem notification={notification} />
											</ListItem>
										))}
									</List>
								</AnimatePresence>
								<AnimatePresence>
									<List>
										{read.map((notification) => (
											<ListItem as={motion.li} key={notification.id}>
												<ProfileNotificationItem notification={notification} />
											</ListItem>
										))}
									</List>
								</AnimatePresence>
							</PopoverBody>
						</PopoverContent>
					</Portal>
				</Popover>
				<List
					spacing={0}
					px={0}
					mt={0}
					mb={3}
					w='full'
					fontSize={{ base: 'sm', lg: 'md' }}
					transition='all 0.3s ease'
				>
					{menuItems.map((item, index) => {
						if (item.isDisabled) return null;

						return (
							<SidebarMenuItem
								key={index}
								icon={item.icon}
								my={0}
								target={item.target}
								isActive={location.pathname === item.target}
								isExpanded={sidebarExpanded}
							>
								{item.label}
							</SidebarMenuItem>
						);
					})}
				</List>

				<Flex justifyContent='space-between' flexWrap='nowrap' w='full' pos='relative'>
					<IconButton
						aria-label='Toggle wide sidebar'
						aria-expanded={sidebarExpanded}
						ml={sidebarExpanded ? '13px' : '10.5px'}
						icon={<FiChevronsLeft />}
						size='xs'
						onClick={() => setSidebarExpanded(!sidebarExpanded)}
						transform={sidebarExpanded ? 'rotate(0deg)' : 'rotate(180deg)'}
						transition='all 0.3s ease'
					/>
					<DarkModeToggle
						showLabel={false}
						showHelperText={false}
						mr={1}
						ml={sidebarExpanded ? 0 : 2}
						w='90px'
						transition='all 0.3s ease'
						color='text.light'
					/>
				</Flex>
			</Flex>
		</Box>
	) : (
		<Box></Box>
	);
}

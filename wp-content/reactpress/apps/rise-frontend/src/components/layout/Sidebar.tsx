import {
	Badge,
	Box,
	Flex,
	IconButton,
	IconProps,
	List,
	Menu,
	MenuButton,
	MenuList,
	Spacer,
	Text,
} from '@chakra-ui/react';
import SidebarMenuItem from '@common/inputs/SidebarMenuItem';
import DarkModeToggle from '@components/DarkModeToggle';
import { SearchContext } from '@context/SearchContext';
import SearchDrawerContext from '@context/SearchDrawerContext';
import SearchDrawer from '@layout/SearchDrawer';
import useUnreadProfileNotifications from '@queries/useProfileNotifications';
import useSavedSearches from '@queries/useSavedSearches';
import useViewer from '@queries/useViewer';
import { useContext } from 'react';
import { IconType } from 'react-icons';
import {
	FiBriefcase,
	FiFolder,
	FiHome,
	FiList,
	FiSearch,
	FiSettings,
	FiStar,
} from 'react-icons/fi';
import { useLocation } from 'react-router-dom';
import ProfileNotificationsIcon from '../common/ProfileNotificationsIcon';
import ProfileNotificationMenuItem from '../ProfileNotificationMenuItem';

interface SidebarMenuItemProps {
	icon: IconType;
	target: string | (() => void);
	label: string;
	isActive?: boolean;
	iconProps?: IconProps;
}

export default function Sidebar() {
	const [{ loggedInId, starredProfiles }] = useViewer();
	const [savedSearches] = useSavedSearches();

	const [unreadProfileNotifications] = useUnreadProfileNotifications(loggedInId);

	const { drawerIsOpen, openDrawer, closeDrawer } = useContext(SearchDrawerContext);

	const {
		search: { results },
	} = useContext(SearchContext);

	const handleDrawerOpen = () => {
		openDrawer();
	};

	const location = useLocation();

	const menuItems: SidebarMenuItemProps[] = [
		{ icon: FiHome, target: `/`, label: 'Dashboard' },
		{ icon: FiSearch, target: handleDrawerOpen, label: 'Search' },
		{ icon: FiBriefcase, target: '/jobs', label: 'Jobs' },
		{
			icon: FiStar,
			target: '/stars',
			label: 'Starred',
			iconProps: {
				fill: starredProfiles && starredProfiles.length > 0 ? 'brand.orange' : 'transparent',
			},
		},
		{
			icon: FiFolder,
			target: '/searches',
			label: 'Searches',
			iconProps: { fill: savedSearches?.length > 0 ? 'brand.orange' : 'transparent' },
		},
	];

	return loggedInId ? (
		<Box id='sidebar' w='auto' py={2} bg='blackAlpha.700' color='text.light'>
			<Flex
				h='full'
				color={'text.light'}
				mt={2}
				mx={0}
				pb={4}
				gap={2}
				flexDirection='column'
				alignItems='center'
				justifyContent='space-between'
			>
				<Menu>
					<MenuButton
						as={IconButton}
						cursor='pointer'
						icon={
							<ProfileNotificationsIcon
								number={unreadProfileNotifications.length}
								iconColor='gray.700'
							/>
						}
						size='sm'
						colorScheme={unreadProfileNotifications.length > 0 ? 'orange' : 'gray'}
						borderRadius='full'
						pos='relative'
						aria-label='Notifications'
						mb={1.5}
					/>
					<MenuList>
						{unreadProfileNotifications.map((notification) => (
							<ProfileNotificationMenuItem key={notification.id} notification={notification} />
						))}
					</MenuList>
				</Menu>

				<List spacing={0} w='full' px={0} mt={3}>
					{menuItems.map((item, index) => (
						<SidebarMenuItem
							key={index}
							icon={item.icon}
							my={0}
							target={item.target}
							isActive={location.pathname === item.target}
							iconProps={item.iconProps}
						>
							<Text>{item.label}</Text>
						</SidebarMenuItem>
					))}

					{results.length ? (
						<SidebarMenuItem icon={FiList} target={'/results'}>
							<Text py={2}>
								Search results{' '}
								<Badge py={1} px={2} borderRadius='full' variant='subtle' colorScheme='orange'>
									{results.length}
								</Badge>
							</Text>
						</SidebarMenuItem>
					) : null}
					<SidebarMenuItem icon={FiSettings} target={'/settings'}>
						<Text>Settings</Text>
					</SidebarMenuItem>
				</List>

				<Spacer />

				<DarkModeToggle showLabel={false} showHelperText={false} justifyContent='center' />
			</Flex>

			<SearchDrawer isOpen={drawerIsOpen} onClose={closeDrawer} />
		</Box>
	) : null;
}

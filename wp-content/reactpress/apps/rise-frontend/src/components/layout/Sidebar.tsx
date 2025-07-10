import { Box, BoxProps, Flex, Icon, IconButton, List, Text } from '@chakra-ui/react';
import SidebarMenuItem from '@common/inputs/SidebarMenuItem';
import DarkModeToggle from '@components/DarkModeToggle';
import { SearchContext } from '@context/SearchContext';
import { useLocalStorage } from '@hooks/hooks';
import useLogout from '@mutations/useLogout';
import useSavedSearches from '@queries/useSavedSearches';
import useViewer from '@queries/useViewer';
import { ReactNode, useContext } from 'react';
import {
	FiBriefcase,
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

export default function Sidebar({ ...props }: BoxProps) {
	const [{ loggedInId, loggedInSlug, starredProfiles }] = useViewer();
	const [savedSearches] = useSavedSearches();

	const {
		search: { results },
	} = useContext(SearchContext);

	const location = useLocation();
	const { logoutMutation } = useLogout();

	const handleLogout = () => {
		logoutMutation().then(() => {
			localStorage.clear();
			window.location.reload();
		});
	};

	const [sidebarExpanded, setSidebarExpanded] = useLocalStorage('sidebarExpanded', false);

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
		{ icon: <Icon as={FiBriefcase} />, target: '/jobs', label: 'Jobs' },
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

	return loggedInId ? (
		<Box
			id='sidebar'
			minH='100%'
			py={0}
			_light={{ bg: 'blackAlpha.700', color: 'text.light' }}
			_dark={{ bg: 'gray.800', color: 'text.light' }}
			overflow='hidden'
			transition='all 200ms ease'
			w={sidebarExpanded ? '160px' : '50px'}
			minW='50px'
			aria-expanded={sidebarExpanded}
			zIndex={1000}
			{...props}
		>
			<Flex
				h='full'
				maxH='100vh'
				mt={0}
				mx={0}
				pt={3}
				pb={4}
				flexDirection='column'
				alignItems='center'
				justifyContent='flex-start'
				borderRight='1px solid'
				transition='all 200ms ease'
				_light={{ borderColor: 'text.dark' }}
				_dark={{ borderColor: 'gray.800' }}
			>
				<IconButton
					aria-label='Toggle wide sidebar'
					aria-expanded={sidebarExpanded}
					icon={<FiChevronsLeft />}
					size='xs'
					onClick={() => setSidebarExpanded(!sidebarExpanded)}
					transform={sidebarExpanded ? 'rotate(0deg)' : 'rotate(180deg)'}
					transition='all 200ms ease'
					alignSelf='flex-start'
					ml={sidebarExpanded ? '13px' : '10.5px'}
				/>

				<List
					spacing={0}
					w='full'
					px={0}
					mt={3}
					mb={2}
					fontSize={{ base: 'xs', lg: 'sm' }}
					transition='all 200ms ease'
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

				<DarkModeToggle
					showLabel={false}
					showHelperText={false}
					justifyContent={sidebarExpanded ? 'flex-start' : 'center'}
					ml={sidebarExpanded ? '12px' : '0'}
					size='md'
					transform='scale(0.9)'
					transition='all 200ms ease'
				/>
			</Flex>
		</Box>
	) : null;
}

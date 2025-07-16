import { Box, BoxProps, Flex, Icon, IconButton, List, Text } from '@chakra-ui/react';
import SidebarMenuItem from '@common/inputs/SidebarMenuItem';
import DarkModeToggle from '@components/DarkModeToggle';
import { SearchContext } from '@context/SearchContext';
import useLogout from '@mutations/useLogout';
import useSavedSearches from '@queries/useSavedSearches';
import useViewer from '@queries/useViewer';
import { ReactNode, useContext, useEffect, useState } from 'react';
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

interface SidebarProps extends BoxProps {
	sidebarExpanded: boolean;
	setSidebarExpanded: (expanded: boolean) => void;
}

export default function Sidebar({ sidebarExpanded, setSidebarExpanded, ...props }: SidebarProps) {
	const [{ loggedInId, loggedInSlug, starredProfiles }] = useViewer();
	const [savedSearches] = useSavedSearches();
	const [sidebarHeight, setSidebarHeight] = useState('100vh');

	const {
		search: { results },
	} = useContext(SearchContext);

	const location = useLocation();
	const { logoutMutation } = useLogout();

	// Calculate sidebar height by subtracting masthead height from 100vh
	useEffect(() => {
		const calculateHeight = () => {
			const masthead = document.getElementById('masthead');
			if (masthead) {
				const mastheadHeight = masthead.offsetHeight;
				setSidebarHeight(`calc(100vh - ${mastheadHeight}px)`);
			}
		};

		// Calculate on mount
		calculateHeight();

		// Recalculate on window resize
		window.addEventListener('resize', calculateHeight);

		return () => {
			window.removeEventListener('resize', calculateHeight);
		};
	}, []);

	const handleLogout = () => {
		logoutMutation().then(() => {
			localStorage.clear();
			window.location.reload();
		});
	};

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
			_light={{ bg: 'blackAlpha.800', color: 'text.light' }}
			_dark={{ bg: 'gray.800', color: 'text.light' }}
			overflow='hidden'
			transition='all 0.3s ease'
			minW='48px'
			aria-expanded={sidebarExpanded}
			{...props}
		>
			<Flex
				h={sidebarHeight}
				w='full'
				mt={0}
				mx={0}
				pt={3}
				flexDirection='column'
				flexWrap='nowrap'
				alignItems='center'
				justifyContent='flex-start'
				borderRight='1px solid'
				transition='all 0.3s ease'
				_light={{ borderColor: 'text.dark' }}
				_dark={{ borderColor: 'gray.800' }}
			>
				<Flex
					justifyContent='space-between'
					flexWrap='nowrap'
					w='full'
					minW='170px'
					left={sidebarExpanded ? 0 : 14}
					pos='relative'
				>
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
					<DarkModeToggle showLabel={false} showHelperText={false} mr={1} w='90px' />
				</Flex>

				<List
					spacing={0}
					w='full'
					px={0}
					my={2}
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
			</Flex>
		</Box>
	) : null;
}

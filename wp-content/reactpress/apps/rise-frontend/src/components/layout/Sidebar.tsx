import { Badge, Box, Flex, Icon, IconButton, List, Spacer, Text } from '@chakra-ui/react';
import SidebarMenuItem from '@common/inputs/SidebarMenuItem';
import DarkModeToggle from '@components/DarkModeToggle';
import { SearchContext } from '@context/SearchContext';
import { useLocalStorage } from '@hooks/hooks';
import useSavedSearches from '@queries/useSavedSearches';
import useViewer from '@queries/useViewer';
import { ReactNode, useContext } from 'react';
import {
	FiBriefcase,
	FiChevronsLeft,
	FiFolder,
	FiHome,
	FiList,
	FiSearch,
	FiSettings,
	FiStar,
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

export default function Sidebar() {
	const [{ loggedInId, starredProfiles }] = useViewer();
	const [savedSearches] = useSavedSearches();

	const {
		search: { results },
	} = useContext(SearchContext);

	const location = useLocation();

	const [sidebarExpanded, setSidebarExpanded] = useLocalStorage('sidebarExpanded', false);

	{
		results.length ? (
			<SidebarMenuItem icon={<Icon as={FiList} />} target='/results'>
				<Text py={2}>
					Search results{' '}
					<Badge py={1} px={2} borderRadius='full' variant='subtle' colorScheme='orange'>
						{results.length}
					</Badge>
				</Text>
			</SidebarMenuItem>
		) : null;
	}

	const menuItems: SidebarMenuItemProps[] = [
		{ icon: <Icon as={FiHome} />, target: `/`, label: 'Dashboard' },
		{ icon: <Icon as={FiSearch} />, target: '/search', label: 'Search' },
		{
			icon: sidebarExpanded ? (
				<Icon as={FiList} />
			) : (
				<Badge
					py={1}
					px={3}
					borderRadius='full'
					variant='subtle'
					fontSize='xs'
					colorScheme='orange'
					pos='relative'
				>
					{results.length}
				</Badge>
			),
			target: '/results',
			label: (
				<Text as='span' m={0} display='block' overflow='hidden' w='200px'>
					{`${results.length} Results`}
				</Text>
			),
			isDisabled: results.length === 0,
		},
		{ icon: <Icon as={FiBriefcase} />, target: '/jobs', label: 'Jobs' },
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
	];

	return loggedInId ? (
		<Box
			id='sidebar'
			w={sidebarExpanded ? '180px' : '52px'}
			py={0}
			_light={{ bg: 'blackAlpha.700', color: 'text.light' }}
			_dark={{ bg: 'blackAlpha.300', color: 'text.light' }}
			overflow='hidden'
			transition='all 200ms ease'
			aria-expanded={sidebarExpanded}
		>
			<Flex
				h='full'
				mt={0}
				mx={0}
				pt={2}
				pb={4}
				flexDirection='column'
				alignItems='center'
				justifyContent='space-between'
				fontSize='sm'
				borderRight='1px solid'
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
					ml='12px'
				/>

				<List spacing={0} w='full' px={0} mt={3} mb={2}>
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

					<SidebarMenuItem
						icon={<Icon as={FiSettings} />}
						target='/settings'
						my={0}
						isExpanded={sidebarExpanded}
					>
						Settings
					</SidebarMenuItem>
				</List>

				<Spacer />

				<DarkModeToggle showLabel={false} showHelperText={false} justifyContent='center' />
			</Flex>
		</Box>
	) : null;
}

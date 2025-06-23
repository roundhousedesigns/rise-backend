import { Box, BoxProps } from '@chakra-ui/react';
import ProfileNotices from '@common/ProfileNotices';
import LoggedIn from '@components/LoggedIn';
import Dashboard from '@routes/Dashboard';
import EditProfile from '@routes/EditProfile';
import FollowedProfiles from '@routes/FollowedProfiles';
import Login from '@routes/Login';
import LostPassword from '@routes/LostPassword';
import NotFound from '@routes/NotFound';
import Profile from '@routes/Profile';
import Register from '@routes/Register';
import ResetPassword from '@routes/ResetPassword';
import Results from '@routes/Results';
import SavedSearches from '@routes/SavedSearches';
import Search from '@routes/Search';
import Settings from '@routes/Settings';
import { Navigate, useRoutes } from 'react-router-dom';

export default function Main({ ...props }: BoxProps) {
	/**
	 * Routes for the main application.
	 */
	const routes = useRoutes([
		{
			path: '/',
			element: (
				<LoggedIn>
					<Dashboard />
				</LoggedIn>
			),
		},
		{
			path: '/reset-password',
			element: <ResetPassword />,
		},
		{
			path: '/profile/:slug',
			element: (
				<LoggedIn>
					<Profile />
				</LoggedIn>
			),
		},
		{
			path: '/profile/edit',
			element: (
				<LoggedIn>
					<EditProfile />
				</LoggedIn>
			),
		},
		{
			path: '/results',
			element: (
				<LoggedIn>
					<Results />
				</LoggedIn>
			),
		},
		{
			path: '/starred',
			element: (
				<LoggedIn>
					<FollowedProfiles />
				</LoggedIn>
			),
		},
		{
			path: '/following',
			element: (
				<LoggedIn>
					<FollowedProfiles />
				</LoggedIn>
			),
		},
		{
			path: '/stars',
			element: <Navigate to='/starred' replace />,
		},
		{
			path: '/search',
			element: <Search />,
		},
		{
			path: '/searches',
			element: (
				<LoggedIn>
					<SavedSearches />
				</LoggedIn>
			),
		},
		{
			path: '/settings',
			element: (
				<LoggedIn>
					<Settings />
				</LoggedIn>
			),
		},
		{
			path: '/login',
			element: <Login />,
		},
		{
			path: '/lost-password',
			element: <LostPassword />,
		},
		{
			path: '/register',
			element: <Register />,
		},
		{
			path: '*',
			element: <NotFound />,
		},
	]);

	return (
		<Box w='full' h='auto' minH='100%' background='none' flex='1 1 auto' {...props}>
			<ProfileNotices />

			<Box px={2}>{routes}</Box>
		</Box>
	);
}

/**
 * useViewer hook. Query information about the current logged in user.
 */

import { ViewerData } from '@/lib/types';
import { gql, useQuery } from '@apollo/client';
import { omit } from 'lodash';

export const QUERY_VIEWER = gql`
	query QueryViewer {
		viewer {
			id: databaseId
			slug
			firstName
			lastName
			email
			username
			isOrg
			disableProfile
			roles {
				nodes {
					name
				}
			}
			starredProfiles(first: 100) {
				nodes {
					databaseId
				}
			}
		}
		networkPartnerManagementLinks {
			addEvent
			listEvents
		}
	}
`;

const useViewer = (): [ViewerData, any] => {
	const result = useQuery(QUERY_VIEWER, {
		fetchPolicy: 'network-only',
	});

	const {
		id: loggedInId,
		slug: loggedInSlug,
		firstName,
		lastName,
		email,
		username,
		disableProfile,
		isOrg,
		roles: userRolesRaw,
		starredProfiles: starredProfilesRaw,
	} = result?.data?.viewer || {};

	const roles = userRolesRaw?.nodes.map((node: { name: string }) => node.name) || [];

	const isNetworkPartner = roles.includes('network-partner');

	const networkPartnerManagementLinks = result?.data?.networkPartnerManagementLinks || {};

	const starredProfiles =
		starredProfilesRaw?.nodes.map((node: { databaseId: number }) => node.databaseId) || [];

	return [
		{
			loggedInId,
			loggedInSlug,
			firstName,
			lastName,
			email,
			username,
			disableProfile,
			isOrg,
			isNetworkPartner,
			networkPartnerManagementLinks,
			roles,
			starredProfiles,
		},
		omit(result, ['data']),
	];
};

export default useViewer;

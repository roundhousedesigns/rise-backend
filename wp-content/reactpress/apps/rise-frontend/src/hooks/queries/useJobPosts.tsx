/**
 * useJobPosts hook. Query to retrieve job posts.
 */

import { gql, useQuery } from '@apollo/client';
import { JobPost, WPItem } from '@lib/classes';
import { omit } from 'lodash';

export const QUERY_JOB_POSTS = gql`
	query JobPostsQuery($ids: [ID] = [], $stati: [PostStatusEnum] = [PENDING, PUBLISH]) {
		jobPosts(where: { in: $ids, stati: $stati }) {
			nodes {
				id: databaseId
				status
				companyName(format: RAW)
				companyAddress(format: RAW)
				contactEmail(format: RAW)
				contactName(format: RAW)
				contactPhone(format: RAW)
				startDate
				description(format: RAW)
				compensation
				isInternship
				isPaid
				isUnion
				endDate
				instructions(format: RAW)
				applicationUrl
				applicationPhone
				applicationEmail
				title
				expirationDate(format: RAW)
				authorNode: author {
					node {
						databaseId
					}
				}
				positions(first: 50) {
					nodes {
						id: databaseId
						parentId: parentDatabaseId
					}
				}
				skills(first: 50) {
					nodes {
						id: databaseId
					}
				}
			}
		}
	}
`;

const useJobPosts = (ids: number[] = []): [JobPost[], any] => {
	const result = useQuery(QUERY_JOB_POSTS, {
		variables: {
			ids: ids.map((id) => id.toString()), // Convert numbers to strings for ID type
		},
		fetchPolicy: 'cache-and-network',
		// Skip the query entirely if we have no IDs
		// TODO figure out why we had this here...with the ids.length === 0, no jobs were being returned
		// skip: ids.length === 0,
	});

	if (!result?.data?.jobPosts?.nodes) {
		return [[], omit(result, ['data'])];
	}

	const jobPosts: JobPost[] =
		result?.data?.jobPosts?.nodes?.map((node: any) => {
			const {
				id,
				status,
				title,
				description,
				companyName,
				contactEmail,
				contactName,
				compensation,
				startDate,
				endDate,
				companyAddress,
				instructions,
				isInternship,
				isPaid,
				isUnion,
				applicationUrl,
				applicationPhone,
				applicationEmail,
				expirationDate,
			} = node;

			const author = node?.authorNode?.node?.databaseId;

			const jobs = node.positions.nodes.filter(
				(position: { __typename: string; id: number; parentId: number }) =>
					position.parentId !== null
			);
			const departments = node.positions.nodes.filter(
				(position: { __typename: string; id: number; parentId: number }) =>
					position.parentId === null
			);

			const job = new JobPost({
				id,
				author,
				status,
				title,
				description,
				companyName,
				contactEmail,
				contactName,
				compensation,
				startDate,
				endDate,
				companyAddress,
				instructions,
				isInternship,
				isPaid,
				isUnion,
				applicationUrl,
				applicationPhone,
				applicationEmail,
				expirationDate,
				positions: {
					departments: departments?.map((department: WPItem) => department.id),
					jobs: jobs?.map((job: WPItem) => job.id),
				},
				skills: node.skills.nodes.map((skill: { id: number }) => skill.id),
			});

			return job;
		}) ?? [];

	return [jobPosts, omit(result, ['data'])];
};

export default useJobPosts;

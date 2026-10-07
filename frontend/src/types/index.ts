export type Status = 'draft' | 'review' | 'published'
export type CommentStatus = 'pending' | 'approved' | 'rejected'

export interface User { id: number; email: string; displayName: string; roles: string[] }
export interface Category { id: number; name: string; slug: string; count: number }
export interface Tag { id: number; name: string; slug: string; count: number }
export interface Post {
  id: number; title: string; slug: string; excerpt: string; content?: string; status: Status
  coverImage?: string | null; views: number; createdAt: string; publishedAt?: string | null
  category: { id: number; name: string; slug: string }
  author: { id: number; displayName: string }
  tags: { id: number; name: string; slug: string }[]
}
export interface Comment {
  id: number; authorName: string; authorEmail: string; body: string; status: CommentStatus
  createdAt: string; post: { id: number; title: string }
}
export interface DashboardStats {
  posts: number; publishedPosts: number; draftPosts: number; reviewPosts: number
  categories: number; tags: number; comments: number; pendingComments: number
}

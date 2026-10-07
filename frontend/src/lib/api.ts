const API =
  (import.meta as ImportMeta & { env: { VITE_API_URL?: string } }).env
    .VITE_API_URL || "/api";

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
  const response = await fetch(`${API}${path}`, {
    credentials: "include",
    headers: { "Content-Type": "application/json", ...(options.headers || {}) },
    ...options,
  });
  const payload = await response.json().catch(() => ({}));
  if (!response.ok)
    throw new Error(
      payload.error || payload.message || `Request failed (${response.status})`,
    );
  return payload as T;
}

export const api = {
  login: (email: string, password: string) =>
    request<{ user: import("../types").User }>("/login", {
      method: "POST",
      body: JSON.stringify({ email, password }),
    }),
  me: () => request<{ user: import("../types").User }>("/me"),
  logout: () => request("/logout", { method: "POST" }),
  dashboard: () => request<import("../types").DashboardStats>("/dashboard"),
  posts: (params = "") =>
    request<{ items: import("../types").Post[] }>(`/posts${params}`),
  post: (id: string | number) =>
    request<import("../types").Post>(`/posts/${id}`),
  createPost: (payload: unknown) =>
    request<import("../types").Post>("/posts", {
      method: "POST",
      body: JSON.stringify(payload),
    }),
  updatePost: (id: number, payload: unknown) =>
    request<import("../types").Post>(`/posts/${id}`, {
      method: "PUT",
      body: JSON.stringify(payload),
    }),
  deletePost: (id: number) => request(`/posts/${id}`, { method: "DELETE" }),
  categories: () =>
    request<{ items: import("../types").Category[] }>("/categories"),
  createCategory: (name: string) =>
    request<import("../types").Category>("/categories", {
      method: "POST",
      body: JSON.stringify({ name }),
    }),
  updateCategory: (id: number, name: string) =>
    request<import("../types").Category>(`/categories/${id}`, {
      method: "PUT",
      body: JSON.stringify({ name }),
    }),
  deleteCategory: (id: number) =>
    request(`/categories/${id}`, { method: "DELETE" }),
  tags: () => request<{ items: import("../types").Tag[] }>("/tags"),
  createTag: (name: string) =>
    request<import("../types").Tag>("/tags", {
      method: "POST",
      body: JSON.stringify({ name }),
    }),
  updateTag: (id: number, name: string) =>
    request<import("../types").Tag>(`/tags/${id}`, {
      method: "PUT",
      body: JSON.stringify({ name }),
    }),
  deleteTag: (id: number) => request(`/tags/${id}`, { method: "DELETE" }),
  comments: () => request<{ items: import("../types").Comment[] }>("/comments"),
  updateCommentStatus: (id: number, status: import("../types").CommentStatus) =>
    request<import("../types").Comment>(`/comments/${id}/status`, {
      method: "PATCH",
      body: JSON.stringify({ status }),
    }),
  deleteComment: (id: number) =>
    request(`/comments/${id}`, { method: "DELETE" }),
  submitComment: (
    id: number,
    payload: { authorName: string; authorEmail: string; body: string },
  ) =>
    request(`/posts/${id}/comments`, {
      method: "POST",
      body: JSON.stringify(payload),
    }),
};

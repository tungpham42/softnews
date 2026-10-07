import React, { createContext, useContext, useEffect, useState } from "react";
import ReactDOM from "react-dom/client";
import {
  BrowserRouter,
  Navigate,
  Route,
  Routes,
  useLocation,
  Link,
  Outlet,
} from "react-router-dom";
import "./index.css";
import { api } from "./lib/api";
import type { User } from "./types";
import AdminLayout from "./layouts/AdminLayout";
import Dashboard from "./pages/admin/Dashboard";
import Posts from "./pages/admin/Posts";
import Categories from "./pages/admin/Categories";
import Tags from "./pages/admin/Tags";
import Comments from "./pages/admin/Comments";
import Settings from "./pages/admin/Settings";
import Users from "./pages/admin/Users";
import Login from "./pages/admin/Login";
import Home from "./pages/public/Home";
import PostDetail from "./pages/public/PostDetail";

const AuthContext = createContext<{
  user: User | null;
  loading: boolean;
  setUser: (u: User | null) => void;
}>({ user: null, loading: true, setUser: () => {} });
export const useAuth = () => useContext(AuthContext);
function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);
  useEffect(() => {
    api
      .me()
      .then((r) => setUser(r.user))
      .catch(() => setUser(null))
      .finally(() => setLoading(false));
  }, []);
  return (
    <AuthContext.Provider value={{ user, loading, setUser }}>
      {children}
    </AuthContext.Provider>
  );
}
function RequireAuth() {
  const { user, loading } = useAuth();
  const location = useLocation();
  if (loading)
    return (
      <div className="grid min-h-screen place-items-center text-sm text-slate-500">
        Loading newsroom...
      </div>
    );
  return user ? (
    <Outlet />
  ) : (
    <Navigate to="/admin/login" replace state={{ from: location.pathname }} />
  );
}
function PublicLayout() {
  return (
    <div className="min-h-screen bg-slate-50">
      <header className="border-b border-slate-200 bg-slate-950 text-white">
        <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 md:px-7">
          <Link
            to="/"
            className="flex items-center gap-3 font-extrabold tracking-tight"
          >
            <span className="grid size-8 place-items-center rounded-xl bg-indigo-500">
              N
            </span>
            Newsroom
          </Link>
          <nav className="hidden gap-6 text-sm font-semibold text-slate-300 md:flex">
            <Link className="hover:text-white" to="/">
              Home
            </Link>
            <Link className="hover:text-white" to="/?category=Technology">
              Technology
            </Link>
            <Link className="hover:text-white" to="/?category=Business">
              Business
            </Link>
            <Link className="hover:text-white" to="/?category=Culture">
              Culture
            </Link>
            <Link className="hover:text-white" to="/?category=World">
              World
            </Link>
          </nav>
          <Link
            to="/admin"
            className="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-bold hover:bg-white/10"
          >
            Admin
          </Link>
        </div>
      </header>
      <main>
        <Outlet />
      </main>
      <footer className="mt-16 border-t border-slate-200 bg-white">
        <div className="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
          <span>Newsroom — independent publishing for a changing world.</span>
          <span>Built with Symfony + React + Tailwind.</span>
        </div>
      </footer>
    </div>
  );
}
ReactDOM.createRoot(document.getElementById("root")!).render(
  <React.StrictMode>
    <BrowserRouter>
      <AuthProvider>
        <Routes>
          <Route path="/" element={<PublicLayout />}>
            <Route index element={<Home />} />
            <Route path="post/:id" element={<PostDetail />} />
          </Route>
          <Route path="/admin/login" element={<Login />} />
          <Route element={<RequireAuth />}>
            <Route path="/admin" element={<AdminLayout />}>
              <Route index element={<Dashboard />} />
              <Route path="posts" element={<Posts />} />
              <Route path="categories" element={<Categories />} />
              <Route path="tags" element={<Tags />} />
              <Route path="comments" element={<Comments />} />
              <Route path="settings" element={<Settings />} />
              <Route path="users" element={<Users />} />
            </Route>
          </Route>
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  </React.StrictMode>,
);

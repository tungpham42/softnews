import { Link, NavLink, Outlet, useNavigate } from 'react-router-dom'
import { useState } from 'react'
import { api } from '../lib/api'
import { Icon, type IconName } from '../components/Icon'
import { useAuth } from '../main'

const items: { to: string; label: string; icon: IconName }[] = [
  { to: '/admin', label: 'Dashboard', icon: 'LayoutDashboard' },
  { to: '/admin/posts', label: 'Posts', icon: 'FileText' },
  { to: '/admin/categories', label: 'Categories', icon: 'Folder' },
  { to: '/admin/tags', label: 'Tags', icon: 'Tag' },
  { to: '/admin/comments', label: 'Comments', icon: 'MessageSquare' },
]

export default function AdminLayout() {
  const { user, setUser } = useAuth()
  const navigate = useNavigate()
  const [open, setOpen] = useState(false)
  async function logout() { await api.logout(); setUser(null); navigate('/admin/login') }
  return (
    <div className="min-h-screen bg-slate-50">
      <header className="sticky top-0 z-30 h-16 border-b border-slate-200 bg-slate-950 text-white">
        <div className="flex h-full items-center justify-between px-4 md:px-7">
          <div className="flex items-center gap-3">
            <button className="rounded-lg p-2 hover:bg-white/10 md:hidden" onClick={() => setOpen(!open)}><Icon name="Menu" size={20} /></button>
            <Link to="/admin" className="flex items-center gap-3 font-bold tracking-tight"><span className="grid size-8 place-items-center rounded-xl bg-indigo-500"><Icon name="FileText" size={17}/></span>Newsroom</Link>
          </div>
          <div className="flex items-center gap-3">
            <Link to="/" className="hidden items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 hover:bg-white/10 md:flex"><Icon name="ExternalLink" size={14}/> View site</Link>
            <div className="grid size-9 place-items-center rounded-xl border border-white/10 bg-white/5 text-xs font-bold">{user?.displayName?.slice(0,1) || 'A'}</div>
            <button onClick={logout} className="rounded-lg p-2 text-slate-300 hover:bg-white/10 hover:text-white"><Icon name="LogOut" size={17}/></button>
          </div>
        </div>
      </header>
      <div className="md:grid md:grid-cols-[240px_1fr]">
        <aside className={`${open ? 'block' : 'hidden'} border-r border-slate-200 bg-white p-4 md:sticky md:top-16 md:block md:h-[calc(100vh-4rem)]`}>
          <p className="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">Workspace</p>
          <nav className="space-y-1">
            {items.map(item => <NavLink key={item.to} to={item.to} end={item.to === '/admin'} onClick={() => setOpen(false)} className={({isActive}) => `flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition ${isActive ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'}`}><Icon name={item.icon} size={17}/>{item.label}</NavLink>)}
          </nav>
          <p className="mb-2 mt-8 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">System</p>
          <nav className="space-y-1"><NavLink to="/admin/settings" className={({isActive})=>`flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold ${isActive?'bg-indigo-50 text-indigo-700':'text-slate-600 hover:bg-slate-50'}`}><Icon name="Settings" size={17}/>Settings</NavLink><NavLink to="/admin/users" className={({isActive})=>`flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold ${isActive?'bg-indigo-50 text-indigo-700':'text-slate-600 hover:bg-slate-50'}`}><Icon name="Users" size={17}/>Users</NavLink></nav>
          <div className="mt-7 rounded-xl border border-dashed border-slate-200 bg-slate-50 p-3 text-[11px] leading-5 text-slate-500">Symfony 8.1 API<br/>Doctrine + MySQL<br/>React + Tailwind CSS 4</div>
        </aside>
        <main className="min-w-0 p-4 md:p-7"><Outlet /></main>
      </div>
    </div>
  )
}

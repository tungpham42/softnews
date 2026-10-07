import { FormEvent, useState } from 'react'
import { useLocation, useNavigate } from 'react-router-dom'
import { api } from '../../lib/api'
import { useAuth } from '../../main'
import { Button, Input } from '../../components/Ui'
import { Icon } from '../../components/Icon'

export default function Login(){
 const [email,setEmail]=useState('admin@newsroom.test');const [password,setPassword]=useState('admin12345');const [error,setError]=useState('');const [busy,setBusy]=useState(false);const {setUser}=useAuth();const navigate=useNavigate();const location=useLocation();
 async function submit(e:FormEvent){e.preventDefault();setError('');setBusy(true);try{const r=await api.login(email,password);setUser(r.user);navigate((location.state as {from?:string})?.from||'/admin',{replace:true})}catch(err){setError(err instanceof Error?err.message:'Unable to sign in.')}finally{setBusy(false)}}
 return <div className="grid min-h-screen place-items-center bg-slate-950 px-4"><div className="w-full max-w-md rounded-3xl border border-white/10 bg-white p-7 shadow-2xl"><div className="mb-7 text-center"><div className="mx-auto mb-4 grid size-12 place-items-center rounded-2xl bg-indigo-600 font-black text-white"><Icon name="FileText" size={22}/></div><h1 className="text-2xl font-black text-slate-900">Sign in to Newsroom</h1><p className="mt-1 text-sm text-slate-500">Manage posts, taxonomy and comments.</p></div><form onSubmit={submit} className="space-y-4"><label className="block"><span className="mb-1.5 block text-xs font-bold text-slate-600">Email</span><Input type="email" autoComplete="email" value={email} onChange={e=>setEmail(e.target.value)} /></label><label className="block"><span className="mb-1.5 block text-xs font-bold text-slate-600">Password</span><Input type="password" autoComplete="current-password" value={password} onChange={e=>setPassword(e.target.value)} /></label>{error&&<div className="rounded-xl bg-rose-50 px-3 py-2.5 text-sm font-semibold text-rose-700">{error}</div>}<Button type="submit" className="w-full" disabled={busy}>{busy?'Signing in...':'Sign in'}</Button></form><div className="mt-5 border-t border-slate-200 pt-4 text-center text-xs text-slate-400">Demo: admin@newsroom.test / admin12345</div></div></div>
}

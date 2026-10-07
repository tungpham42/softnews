import { type LucideProps, LayoutDashboard, FileText, Folder, Tag, MessageSquare, Settings, Users, Plus, Search, Pencil, Trash2, Eye, Check, X, ChevronDown, LogOut, ArrowRight, CalendarDays, Menu, BarChart3, ExternalLink } from 'lucide-react'

const icons = { LayoutDashboard, FileText, Folder, Tag, MessageSquare, Settings, Users, Plus, Search, Pencil, Trash2, Eye, Check, X, ChevronDown, LogOut, ArrowRight, CalendarDays, Menu, BarChart3, ExternalLink }
export type IconName = keyof typeof icons
export function Icon({ name, ...props }: LucideProps & { name: IconName }) {
  const Comp = icons[name]
  return <Comp aria-hidden="true" {...props} />
}

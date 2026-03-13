import { useToast as useVueToastification } from 'vue-toastification'

export function useToast() {
  const toast = useVueToastification()
  
  return {
    success: (message, title = 'Berhasil') => {
      toast.success(message, {
        title,
        icon: {
          iconClass: 'fas fa-check-circle',
          iconChildren: '',
          iconTag: 'i'
        }
      })
    },
    error: (message, title = 'Error') => {
      toast.error(message, {
        title,
        icon: {
          iconClass: 'fas fa-exclamation-circle',
          iconChildren: '',
          iconTag: 'i'
        }
      })
    },
    warning: (message, title = 'Peringatan') => {
      toast.warning(message, {
        title,
        icon: {
          iconClass: 'fas fa-exclamation-triangle',
          iconChildren: '',
          iconTag: 'i'
        }
      })
    },
    info: (message, title = 'Informasi') => {
      toast.info(message, {
        title,
        icon: {
          iconClass: 'fas fa-info-circle',
          iconChildren: '',
          iconTag: 'i'
        }
      })
    },
    // For backward compatibility
    default: (message, title = 'Notifikasi') => {
      toast(message, {
        title,
        icon: {
          iconClass: 'fas fa-bell',
          iconChildren: '',
          iconTag: 'i'
        }
      })
    }
  }
} 
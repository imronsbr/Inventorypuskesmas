import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

export function useConfirm() {
  return async ({
    title = 'Konfirmasi',
    text = 'Apakah Anda yakin ingin melanjutkan?',
    confirmButtonText = 'Ya, Simpan',
    cancelButtonText = 'Batal',
    icon = 'question',
    ...rest
  } = {}) => {
    const result = await Swal.fire({
      title,
      text,
      icon,
      showCancelButton: true,
      confirmButtonColor: '#2563eb',
      cancelButtonColor: '#d1d5db',
      confirmButtonText,
      cancelButtonText,
      customClass: {
        popup: 'rounded-xl',
        confirmButton: 'btn btn-primary',
        cancelButton: 'btn btn-outline ml-2',
      },
      ...rest
    })
    return result.isConfirmed
  }
} 
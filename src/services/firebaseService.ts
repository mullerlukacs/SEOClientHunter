import { 
  signInWithEmailAndPassword, 
  createUserWithEmailAndPassword, 
  signOut, 
  onAuthStateChanged,
  User as FirebaseUser,
  GoogleAuthProvider,
  signInWithPopup
} from 'firebase/auth';
import { 
  collection, 
  doc, 
  setDoc, 
  getDocs, 
  getDoc,
  query, 
  where, 
  deleteDoc, 
  updateDoc 
} from 'firebase/firestore';
import { auth, db } from '../firebase';
import { Lead, UserProfile } from '../types';
import { generateProspectsForSearch } from './seoEngine';

// Auth State Listener
export function subscribeToAuth(callback: (user: FirebaseUser | null) => void) {
  return onAuthStateChanged(auth, callback);
}

// Email/Password Login
export async function loginWithEmail(email: string, pass: string): Promise<FirebaseUser> {
  const cred = await signInWithEmailAndPassword(auth, email, pass);
  return cred.user;
}

// Email/Password Register
export async function registerWithEmail(email: string, pass: string, name: string): Promise<FirebaseUser> {
  const cred = await createUserWithEmailAndPassword(auth, email, pass);
  const user = cred.user;

  // Save profile to Firestore
  const profile: UserProfile = {
    uid: user.uid,
    email: user.email || email,
    name: name || email.split('@')[0],
    role: email.toLowerCase().includes('admin') ? 'admin' : 'user',
    createdAt: new Date().toISOString()
  };

  try {
    await setDoc(doc(db, 'users', user.uid), profile);
  } catch (err) {
    console.error('Failed to create user profile in Firestore:', err);
  }

  return user;
}

// Google Sign-In
export async function loginWithGoogle(): Promise<FirebaseUser> {
  const provider = new GoogleAuthProvider();
  const cred = await signInWithPopup(auth, provider);
  return cred.user;
}

// Logout
export async function logoutUser(): Promise<void> {
  await signOut(auth);
}

// Firestore Leads CRUD
export async function saveLeadToFirestore(lead: Lead): Promise<void> {
  try {
    await setDoc(doc(db, 'leads', lead.id), lead);
  } catch (err) {
    console.warn('Firestore write warning:', err);
  }
}

export async function saveBatchLeadsToFirestore(leads: Lead[]): Promise<void> {
  try {
    for (const lead of leads) {
      await setDoc(doc(db, 'leads', lead.id), lead);
    }
  } catch (err) {
    console.warn('Batch Firestore write warning:', err);
  }
}

export async function fetchLeadsFromFirestore(userId: string): Promise<Lead[]> {
  try {
    const q = query(collection(db, 'leads'), where('userId', '==', userId));
    const snap = await getDocs(q);
    const results: Lead[] = [];
    snap.forEach(d => {
      results.push(d.data() as Lead);
    });

    // If user has no leads yet in Firestore, seed with 6 realistic high-opportunity demo leads
    if (results.length === 0) {
      const seeded = generateProspectsForSearch(userId, 'Dentist', 'New York', 'United States', 6);
      for (const s of seeded) {
        await setDoc(doc(db, 'leads', s.id), s);
      }
      return seeded;
    }

    return results;
  } catch (err) {
    console.warn('Firestore fetch failed, returning local demo leads:', err);
    return generateProspectsForSearch(userId, 'Dentist', 'New York', 'United States', 6);
  }
}

export async function updateLeadStatusInFirestore(leadId: string, status: Lead['status']): Promise<void> {
  try {
    await updateDoc(doc(db, 'leads', leadId), { status, updatedAt: new Date().toISOString() });
  } catch (err) {
    console.warn('Failed to update lead status:', err);
  }
}

export async function updateLeadNotesInFirestore(leadId: string, notes: string): Promise<void> {
  try {
    await updateDoc(doc(db, 'leads', leadId), { notes, updatedAt: new Date().toISOString() });
  } catch (err) {
    console.warn('Failed to update lead notes:', err);
  }
}

export async function deleteLeadFromFirestore(leadId: string): Promise<void> {
  try {
    await deleteDoc(doc(db, 'leads', leadId));
  } catch (err) {
    console.warn('Failed to delete lead from Firestore:', err);
  }
}

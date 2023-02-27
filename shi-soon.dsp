   for (i=0; i < 1023 && 5+77*i < 0xd800; ++i)
      buffer7[i] = 5+77*i;
   buffer7[i++] = 0xd801;
   buffer7[i++] = 0xdc02;
   buffer7[i++] = 0xdbff;
   buffer7[i++] = 0xdfff;
   buffer7[i] = 0;
   p = stb_to_utf8(buffer8, buffer7, sizeof(buffer8));
   c(p != NULL, "stb_to_utf8");
   if (p != NULL) {
      stb_from_utf8(buffer9, buffer8, sizeof(buffer9)/2);
      c(!memcmp(buffer7, buffer9, i*2), "stb_from_utf8");
   }

   z = "foo.*[bd]ak?r";
   c( stb_regex(z, "muggle man food is barfy") == 1, "stb_regex 1");
   c( stb_regex("foo.*bar", "muggle man food is farfy") == 0, "stb_regex 2");
   c( stb_regex("[^a-zA-Z]foo[^a-zA-Z]", "dfoobar xfood") == 0, "stb_regex 3");
   c( stb_regex(z, "muman foob is bakrfy") == 1, "stb_regex 4");
   z = "foo.*[bd]bk?r";
   c( stb_regex(z, "muman foob is bakrfy") == 0, "stb_regex 5");
   c( stb_regex(z, "muman foob is bbkrfy") == 1, "stb_regex 6");

   stb_regex(NULL,NULL);

   #if 0
   test_parser_generator();
   stb_wrapper_listall(dumpfunc);
   if (alloc_num) 
      printf("Memory still in use: %d allocations of %d bytes.\n", alloc_num, alloc_size);
   #endif

   test_script();
   p = stb_file("sieve.stua", NULL);
   if (p) {
      stua_run_script(p);      
      free(p);
   }
   stua_uninit();

   //stb_wrapper_listall(dumpfunc);
   printf("Memory still in use: %d allocations of %d bytes.\n", alloc_num, alloc_size);

   c(stb_alloc_count_alloc == stb_alloc_count_free, "stb_alloc 0");

   bst_test();

   c(stb_alloc_count_alloc == stb_alloc_count_free, "stb_alloc 0");





#if 0 // parser generator
//////////////////////////////////////////////////////////////////////////
//
//   stb_parser
//
//   Generates an LR(1) parser from a grammar, and can parse with it



// Symbol representations
//
// Client:     Internal:
//    -           c=0     e aka epsilon
//    -           c=1     $ aka end of string
//   > 0        2<=c<M    terminals (note these are remapped from a sparse layout)
//   < 0        M<=c<N    non-terminals

#define END 1
#define EPS 0

short encode_term[4096];  // @TODO: malloc these
short encode_nonterm[4096];
int first_nonterm, num_symbols, symset;
#define encode_term(x)     encode_term[x]
#define encode_nonterm(x)  encode_nonterm[~(x)]
#define encode_symbol(x)   ((x) >= 0 ? encode_term(x) : encode_nonterm(x))

stb_bitset **compute_first(short ** productions)
{
   int i, changed;
   stb_bitset **first = malloc(sizeof(*first) * num_symbols);

   assert(symset);
   for (i=0; i < num_symbols; ++i)
      first[i] = stb_bitset_new(0, symset);

   for (i=END; i < first_nonterm; ++i)
      stb_bitset_setbit(first[i], i);

   for (i=0; i < stb_arr_len(productions); ++i) {
      if (productions[i][2] == 0) {
         int nt = encode_nonterm(productions[i][0]);
         stb_bitset_setbit(first[nt], EPS);
      }
   }

   do {
      changed = 0;
      for (i=0; i < stb_arr_len(productions); ++i) {
         int j, nt = encode_nonterm(productions[i][0]);
         for (j=2; productions[i][j]; ++j) {
            int z = encode_symbol(productions[i][j]);
            changed |= stb_bitset_unioneq_changed(first[nt], first[z], symset);
            if (!stb_bitset_testbit(first[z], EPS))
               break;
         }
         if (!productions[i][j] && !stb_bitset_testbit(first[nt], EPS)) {
            stb_bitset_setbit(first[nt], EPS);
            changed = 1;
         }
      }
   } while (changed);
   return first;
}



typedef stb_summary_tree2 * stb__stree2;
stb_define_sort(stb__summarysort, stb__stree2, (*a)->makes_target_weight < (*b)->makes_target_weight)

void *stb_summarize_tree(void *tree, int limit, float reweight)
{
   int i,j,k;
   STB__ARR(stb_summary_tree *) ret=NULL;
   STB__ARR(stb_summary_tree2 *) all=NULL;

   // first clone the tree so we can manipulate it
   stb_summary_tree2 *t = stb__summarize_clone((stb_summary_tree *) tree);
   if (reweight < 1) reweight = 1;

   // now compute how far up the tree each node would get pushed
   // there's no value in pushing a node up to an empty node with
   // only one child, so we keep pushing it up
   stb__summarize_compute_targets(NULL, t, reweight, 1);

   all = stb__summarize_make_array(all, t);

   // now we want to iteratively find the smallest 'makes_target_weight',
   // update that, and then fix all the others (which will be all descendents)
   // to do this efficiently, we need a heap or a sorted binary tree
   // what we have is an array. maybe we can insertion sort the array?
   stb__summarysort(all, stb_arr_len(all));

   for (i=0; i < stb_arr_len(all) - limit; ++i) {
      stb_summary_tree2 *src, *dest;
      src = all[i];
      dest = all[i]->target;
      if (src->makes_target_weight == 0) continue;
      assert(dest != NULL);

      for (k=0; k < stb_arr_len(all); ++k)
         if (all[k] == dest)
            break;
      assert(k != stb_arr_len(all));
      assert(i < k);

      // move weight from all[i] to target
      src->weight = dest->makes_target_weight;
      src->weight = 0;
      src->makes_target_weight = 0;
      // recompute effect of other descendents
      for (j=0; j < stb_arr_len(dest->targeters); ++j) {
         if (dest->targeters[j]->weight) {
            dest->targeters[j]->makes_target_weight = dest->weight + dest->targeters[j]->weight_at_target;
            assert(dest->targeters[j]->makes_target_weight <= dest->weight_with_all_children);
         }
      }
      STB_(stb__summarysort,_ins_sort)(all+i, stb_arr_len(all)-i);
   }
   // now the elements in [ i..stb_arr_len(all) ) are the relevant ones
   for (; i < stb_arr_len(all); ++i)
      stb_arr_push(ret, all[i]->original);

   // now free all our temp data
   for (i=0; i < stb_arr_len(all); ++i) {
      stb_arr_free(all[i]->children);
      free(all[i]);
   }
   stb_arr_free(all);
   return ret;
}
#endif

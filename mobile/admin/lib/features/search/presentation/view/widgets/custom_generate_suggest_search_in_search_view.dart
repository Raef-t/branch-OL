import 'package:flutter/material.dart';
import '/core/lists/search_data_in_search_view_list.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/features/search/presentation/view/widgets/custom_suggest_search_in_search_view.dart';

class CustomGenerateSuggestSearchInSearchView extends StatelessWidget {
  const CustomGenerateSuggestSearchInSearchView({super.key});

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left35AndRight25(
      context: context,
      child: Column(
        children: List.generate(searchDataInSearchViewList.length, (index) {
          final text = searchDataInSearchViewList[index];
          return CustomSuggestSearchInSearchView(text: text, index: index);
        }),
      ),
    );
  }
}

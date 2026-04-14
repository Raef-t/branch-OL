import 'package:flutter/material.dart';
import '/core/lists/search_data_in_search_view_list.dart';
import '/core/sized_boxs/heights.dart';

class CustomSuggestSearchInSearchView extends StatelessWidget {
  const CustomSuggestSearchInSearchView({
    super.key,
    required this.text,
    required this.index,
  });
  final String text;
  final int index;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // CustomTwoImagesWithTextInSearchView(text: text),
        if (index != (searchDataInSearchViewList.length - 1))
          Heights.height14(context: context),
      ],
    );
  }
}
